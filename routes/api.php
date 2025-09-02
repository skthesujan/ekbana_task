<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiKey;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyCategoryController;

Route::middleware([CheckApiKey::class])->group(function () {
    Route::apiResource('category', CompanyCategoryController::class);
    Route::apiResource('company', CompanyController::class);
});