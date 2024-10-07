<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SwhArchiveController;
use App\Http\Controllers\SwhDepositController;
use Illuminate\Support\Facades\Route;

Auth::routes([ "verify" => true ]);

Route::get("/", [ HomeController::class, "index" ])->name("home");

Route::group([ "middleware" => ["auth", "verified"] ], function() {
    Route::group([ "prefix" => "/deposits" ], function() {
        Route::get("/", [ SwhDepositController::class, "index" ])->name("swh-deposits.index");
        Route::get("/new", [ SwhDepositController::class, "showNew" ])->name("swh-deposits.new");
        Route::post("/new", [ SwhDepositController::class, "uploadNew" ])->name("swh-deposits.new.upload");
        Route::get("/{deposit:uuid}", [ SwhDepositController::class, "showDeposit" ])->name("swh-deposits.show");
    });

    Route::group([ "prefix" => "/archives" ], function() {
        Route::get("/", [ SwhArchiveController::class, "index" ])->name("swh-archives.index");
        Route::post("/", [ SwhArchiveController::class, "saveNew" ])->name("swh-archives.new");
    });

    Route::group([ "prefix" => "/account" ], function() {
        Route::get("/", [ AccountController::class, "index" ])->name("account.index");
        Route::post("/", [ AccountController::class, "save" ])->name("account.save");
        Route::post("/change-password", [ AccountController::class, "changePassword" ])->name("account.change-password");
    });
});
