<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuardianReportController;

// Protect the routes with Sanctum tokens
Route::middleware('auth:sanctum')->group(function () {
    
    // The route for the mobile app to get the list of reports
    Route::get('/guardian/reports', [GuardianReportController::class, 'index']);
    
    // The route for the mobile app to view one specific report
    Route::get('/guardian/reports/{id}', [GuardianReportController::class, 'show']);

});