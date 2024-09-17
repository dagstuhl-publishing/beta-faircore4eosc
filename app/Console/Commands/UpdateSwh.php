<?php

namespace App\Console\Commands;

use App\Models\SwhArchive;
use App\Models\SwhDeposit;
use Carbon\CarbonImmutable;
use Dagstuhl\SwhArchiveClient\ApiClient\SwhWebApiClient;
use Dagstuhl\SwhArchiveClient\Repositories\Repository;
use Dagstuhl\SwhArchiveClient\Repositories\RepositoryNode;
use Dagstuhl\SwhArchiveClient\SwhObjects\Origin;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveRequest;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveRequestStatus;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveTaskStatus;
use Dagstuhl\SwhDepositClient\SwhDepositClient;
use Dagstuhl\SwhDepositClient\SwhDepositException;
use Dagstuhl\SwhDepositClient\SwhDepositMetadata;
use Dagstuhl\SwhDepositClient\SwhDepositStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \Exception;

class UpdateSwh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "app:update-swh";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update pending archives and deposits";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->updateArchives();
        $this->updateDeposits();
    }

    public function updateArchives()
    {
        $client = SwhWebApiClient::getCurrent();

        $last = 0;
        while(!($archives = $this->getPendingArchives($last))->isEmpty()) {
            foreach($archives as $archive) {
                $prefix = "archive #{$archive->id}";
                try {
                    if($archive->saveRequestId === null) {
                        Log::info("{$prefix}: creating save request");

                        $repo = Repository::fromNodeUrl($archive->originUrl);
                        $origin = Origin::fromRepository($repo);
                        $saveRequest = $origin->postSaveRequest();
                        if($saveRequest === null) {
                            throw $client->getException();
                        }

                        Log::info("{$prefix}: requested as {$saveRequest->id}");

                        $archive->saveRequestId = $saveRequest->id;

                    } else {
                        Log::info("{$prefix}: checking status");
                        $saveRequest = SaveRequest::byId($archive->saveRequestId);
                        if($saveRequest === null) {
                            throw $client->getException();
                        }
                    }

                    Log::info("{$prefix}: status: {$saveRequest->saveRequestStatus->value} / {$saveRequest->saveTaskStatus->value}");

                    $archive->saveRequestStatus = $saveRequest->saveRequestStatus->value;
                    $archive->saveTaskStatus = $saveRequest->saveTaskStatus->value;
                    $archive->visitStatus = $saveRequest->visitStatus;
                    $archive->swhId = $saveRequest->snapshotSwhId;

                    if($archive->saveTaskStatus === SaveTaskStatus::SUCCEEDED->value) {
                        $snapshot = $saveRequest->getSnapshot();
                        $repoNode = new RepositoryNode($saveRequest->originUrl);
                        $context = $snapshot->getContext($repoNode);
                        $archive->swhIdContext = $context->getIdentifier();
                    }

                    switch($saveRequest->saveRequestStatus) {
                    case SaveRequestStatus::REJECTED:
                        $archive->finished_at = CarbonImmutable::now();
                        break;
                    default:
                        switch($saveRequest->saveTaskStatus) {
                        case SaveTaskStatus::SUCCEEDED:
                        case SaveTaskStatus::FAILED:
                            $archive->finished_at = CarbonImmutable::now();
                            break;
                        default:
                            break;
                        }
                        break;
                    }

                    $archive->save();

                } catch(Exception $ex) {
                    Log::error($ex);
                    //TODO save response?
                }

                $last = $archive->id;
            }
        }
    }

    private function getPendingArchives($last)
    {
        return SwhArchive::whereNull("finished_at")
            ->where("id", ">", $last)
            ->limit(4096)
            ->get();
    }

    public function updateDeposits()
    {
        $client = new SwhDepositClient(
            config("swh.deposit_api.url"),
            config("swh.deposit_api.username"),
            config("swh.deposit_api.password"),
        );
        $collectionName = config("swh.deposit_api.collection_name");

        $last = 0;
        while(!($deposits = $this->getPendingDeposits($last))->isEmpty()) {
            foreach($deposits as $deposit) {
                $prefix = "deposit #{$deposit->id} ({$deposit->uuid})";
                try {
                    if($deposit->depositId === null) {
                        Log::info("{$prefix}: depositing");

                        $metadata = SwhDepositMetadata::fromCodemetaJson($deposit->codemetaJson);
                        $metadata->fillMissingMetadata();
                        //$metadata->add("atom:title", [], json_decode($deposit->codemetaJson)->name);

                        if($deposit->archivePath !== null) {
                            $depositMetadata = $metadata->add("swhdeposit:deposit");
                            $createOrigin = $depositMetadata->add("swhdeposit:create_origin");
                            $createOrigin->add("swhdeposit:origin", [
                                "url" => "https://submission-dev.dagstuhl.de/deposit/{$deposit->uuid}", //TODO
                            ]);
                            $storagePath = "public/deposits/{$deposit->archivePath}";
                            $archive = Storage::readStream($storagePath);
                            $res = $client->createDeposit($collectionName, true, $metadata, $deposit->archiveContentType, $archive);

                        } else {
                            $depositMetadata = $metadata->add("swhdeposit:deposit");
                            $reference = $depositMetadata->add("swhdeposit:reference");
                            $reference->add("swhdeposit:object", [ "swhid" => $deposit->originSwhId ]); 
                            $res = $client->createDeposit($collectionName, true, $metadata);
                        }

                        Log::info("{$prefix}: deposited as {$res->getDepositId()}");

                        $deposit->atomXml = $metadata->generateDOMDocument()->saveXML();
                        $deposit->depositId = $res->getDepositId();
                        $deposit->deposited_at = CarbonImmutable::now();

                    } else {
                        Log::info("{$prefix}: checking status");
                        $res = $client->getStatus($collectionName, $deposit->depositId);
                    }

                    $status = $res->getDepositStatus();
                    Log::info("{$prefix}: status: {$status->value}");

                    $deposit->latestResponseStatus = $res->getResponseStatus();
                    $deposit->latestResponseBody = $res->getResponseBody();
                    $deposit->depositStatus = $status->value;
                    $deposit->depositSwhId = $res->getDepositSwhId();
                    $deposit->depositSwhIdContext = $res->getDepositSwhIdContext();

                    if($status->isFinal()) {
                        $deposit->finished_at = CarbonImmutable::now();
                    }

                    $deposit->save();

                } catch(SwhDepositException $ex) {
                    Log::error($ex);
                    $deposit->latestResponse = $ex->getSwhDepositResponse()->getResponseBody();
                    $deposit->save();

                } catch(Exception $ex) {
                    Log::error($ex);
                }

                $last = $deposit->id;
            }
        }

    }

    private function getPendingDeposits($last)
    {
        return SwhDeposit::whereNull("finished_at")
            ->where("id", ">", $last)
            ->limit(4096)
            ->get();
    }
}
