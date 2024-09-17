<?php

namespace App\Http\Controllers;

use App\Models\SwhArchive;
use Illuminate\Http\Request;

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
        //TODO input validation

        $user = $request->user();

        $archive = new SwhArchive();
        $archive->user()->associate($user);
        $archive->originUrl = $request->input("originUrl");
        $archive->save();

        return redirect()->route("swh-archives.index");
    }
}
