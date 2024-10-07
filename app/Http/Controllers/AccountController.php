<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return view("pages.account.index", [
            "user" => $request->user(),
        ]);
    }

    public function save(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "name" => [ "bail", "required", "string", "min:1" ],
            "notify_archives" => [],
            "notify_deposits" => [],
        ])->stopOnFirstFailure()->validated();

        $user = $request->user();
        $user->name = $validated["name"];
        $user->notify_archives = isset($validated["notify_archives"]);
        $user->notify_deposits = isset($validated["notify_deposits"]);
        $user->save();

        return back()->with("success", "Settings changed successfully.");
    }

    public function changePassword(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "old_password" => [ "bail", "required", "string", "current_password" ],
            "new_password" => [ "bail", "required", "string", "min:8", "confirmed" ],
        ])->stopOnFirstFailure()->validated();

        $user = $request->user();
        $user->password = Hash::make($validated["new_password"]);
        $user->save();

        return back()->with("success", "Password changed successfully.");
    }
}
