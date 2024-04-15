<?php

use App\Http\Controllers\Api\V1\RosterController;
use App\Http\Controllers\Api\V1\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    "prefix" => "v1"
], function () {
    Route::controller(UserAuthController::class)->group(function () {
        Route::post("/register", "register")
            ->name("api.v1.register")
        ;

        Route::post("/login", "login")
            ->name("api.v1.login")
        ;

        Route::post("/logout", "logout")
            ->name("api.v1.logout")
            ->middleware("auth:sanctum")
        ;
    });

    Route::controller(RosterController::class)->group(function () {
        Route::post("/upload-roster", "uploadRoster")
            ->name("api.v1.upload_roster")
            ->middleware("auth:sanctum")
        ;

        Route::get("/activities", "getActivities")
            ->name("api.v1.activities")
            ->middleware("auth:sanctum")
        ;
    });
});
