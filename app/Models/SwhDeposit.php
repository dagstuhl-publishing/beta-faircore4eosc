<?php

namespace App\Models;

use App\Modules\CodeMeta\CodeMetaRecord;
use App\Modules\SwhDepositDataProvider;
use App\Modules\Utils;
use Dagstuhl\DataCite\Metadata\DataCiteRecord;
use Dagstuhl\Latex\Bibliography\BibEntry;
use Dagstuhl\SwhDepositClient\SwhDepositStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SwhDeposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            "depositStatus" => SwhDepositStatus::class,
            'deposited_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDoi(): string
    {
        return "10.0000/deposit-".$this->uuid;
    }

    public function getUrl(): string
    {
        return route("swh-deposits.show", [ "deposit" => $this ]);
    }

    public function getFormattedArchiveSize(): string
    {
        return Utils::formatFileSize($this->archiveSize);
    }

    public function getBrowseUrl(): string
    {
        if($this->depositSwhIdContext === null) {
            return "#";
        }
        return "https://webapp.staging.swh.network/{$this->depositSwhIdContext}";
    }

    public function getCodeMetaRecord(): CodeMetaRecord
    {
        return CodeMetaRecord::fromJson($this->codemetaJson);
    }

    public function exportDataCiteRecord(): DataCiteRecord
    {
        $dataProvider = new SwhDepositDataProvider($this);
        $dataCiteRecord = DataCiteRecord::fromDataProvider($dataProvider);
        return $dataCiteRecord;
    }

    public function exportBibEntry(bool $bibLatex = false): BibEntry
    {
        $codeMetaRecord = $this->getCodeMetaRecord();
        $publishedAt = ($codeMetaRecord->dateCreated ?? $this->created_at);

        $entry = new BibEntry();
        $entry->setType($bibLatex ? "software" : "misc");
        $entry->setKey(Str::slug($codeMetaRecord->name, "_")."_".explode("-", $this->uuid)[0]);

        $entry->setField("title", $codeMetaRecord->name);
        $entry->setField("author", implode(" and ", array_map(fn($author) => "{$author->givenName} {$author->familyName}", $codeMetaRecord->authors)));
        $entry->setField("publisher", SwhDepositDataProvider::PUBLISHER);
        $entry->setField("year", $publishedAt->year);
        $entry->setField("month", strtolower($publishedAt->format("M")));

        if($bibLatex) {
            if($codeMetaRecord->version !== null) {
                $entry->setField("version", $codeMetaRecord->version);
            }
            if($this->depositSwhId !== null) {
                $entry->setField("swhId", $this->depositSwhIdContext);
            }

            $licenses = [];
            foreach($codeMetaRecord->licenses as $license) {
                if(preg_match('/^https?:\\/\\/spdx.org\\/licenses\\/(.*)$/', $license, $matches)) {
                    $licenses[] = $matches[1];
                }
            }
            if(!empty($licenses)) {
                $entry->setField("license", implode(" OR ", $licenses));
            }

        } else {
            $parts = [ "Software" ];
            if($codeMetaRecord->version !== null) {
                $parts[] = "version {$codeMetaRecord->version}";
            }
            if($this->depositSwhId !== null) {
                $parts[] = "swhId: \\href{{$this->depositSwhIdContext}}{\\texttt{{$this->depositSwhId}}} (visited on ".$this->deposited_at->format("Y-m-d").")";
            }
            $entry->setField("note", implode(", ", $parts));
        }

        $entry->setField("doi", $this->getUrl());
        $entry->setField("url", "https://doi.org/".$this->getDoi());

        return $entry;
    }
}
