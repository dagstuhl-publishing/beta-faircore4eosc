<?php

namespace App\Http\Controllers;

use App\Models\SwhDeposit;
use App\Modules\CodeMeta\CodeMetaRecord;
use App\Modules\Utils;
use Composer\Spdx\SpdxLicenses;
use Dagstuhl\SwhDepositClient\SwhDepositResponse;
use Dagstuhl\SwhDepositClient\SwhDepositStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SwhDepositController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $deposits = $user->swhDeposits()
            ->orderByDesc("id")
            ->paginate(20);

        return view("pages.swh-deposits.index", [
            "deposits" => $deposits,
        ]);
    }

    public function showNew(Request $request)
    {
        $spdxLicenses = new SpdxLicenses();
        $licenses = [];
        foreach($spdxLicenses->getLicenses() as $license) {
            $licenses[$license[0]] = $license[1];
        }
        return view("pages.swh-deposits.new", [
            "licenses" => $licenses,
            "languages" => Utils::getLanguageList(),
            "swhId" => $request->input("swhId"),
        ]);
    }

    public function uploadNew(Request $request)
    {
        $user = $request->user();

        $deposit = new SwhDeposit();
        $deposit->uuid = Str::uuid();
        $deposit->user()->associate($user);

        switch($request->input("type")) {
        case "archive":
            $input = Validator::make($request->all(), [
                "archive" => [ "required", "file", "extensions:zip,tar" ],
                "codemetaJson" => [ "required", "string", "json" ],
            ])->stopOnFirstFailure()->validated();

            $file = $input["archive"];
            $extension = $file->extension();
            $filename = "{$deposit->uuid}.{$extension}";
            $path = substr($filename, 0, 2)."/".substr($filename, 2, 2);

            $storagePath = "public/deposits/{$path}";
            Storage::makeDirectory($storagePath);
            $file->storeAs($storagePath, $filename);

            $deposit->archiveFilename = $file->getClientOriginalName();
            $deposit->archiveContentType = $file->getMimeType();
            $deposit->archiveSize = $file->getSize();
            $deposit->archivePath = "{$path}/{$filename}";
            break;

        case "metadata":
            $input = Validator::make($request->all(), [
                "originSwhId" => [ "required", "string" ],
                "codemetaJson" => [ "required", "string", "json" ],
            ])->stopOnFirstFailure()->validated();

            $deposit->originSwhId = $input["originSwhId"];
            break;

        default:
            abort(400);
        }

        $record = CodeMetaRecord::fromJson($input["codemetaJson"]);
        if($record->name === null) {
            throw ValidationException::withMessages([
                "codemetaJson" => "Metadata must contain a name.",
            ]);
        }
        if(empty($record->authors)) {
            throw ValidationException::withMessages([
                "codemetaJson" => "Metadata must contain at least one author.",
            ]);
        }

        $deposit->codemetaJson = $input["codemetaJson"];
        $deposit->save();

        return redirect()->route("swh-deposits.show", ["deposit" => $deposit])
            ->with("success", "Deposit uploaded");
    }

    public function showDeposit(SwhDeposit $deposit)
    {
        //dd(json_decode($deposit->codemetaJson));
        //dd($deposit->getCodeMetaRecord());
        return view("pages.swh-deposits.show", [
            "deposit" => $deposit,
            "depositResponse" => $deposit->latestResponseBody === null ? null : SwhDepositResponse::fromResponseBody($deposit->latestResponseBody),
        ]);
    }
}
