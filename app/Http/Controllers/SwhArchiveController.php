<?php

namespace App\Http\Controllers;

use App\Models\SwhArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SwhArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $archives = $user->swhArchives()
            ->orderByDesc("id")
            ->paginate(20);

        return view("pages.swh-archives.index", [
            "archives" => $archives,
        ]);
    }

    public function saveNew(Request $request)
    {
        $input = Validator::make($request->all(), [
            "originUrl" => [ "required", "url" ],
        ])->stopOnFirstFailure()->validated();

        //TODO validate url?

        $user = $request->user();

        $archive = new SwhArchive();
        $archive->user()->associate($user);
        $archive->originUrl = $input["originUrl"];
        $archive->save();

        return redirect()->route("swh-archives.index");
    }
}
