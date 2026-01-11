<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditReportController;

Route::get('/export/credit-report', [CreditReportController::class, 'export']);


Route::get('/', function () {
    return view('welcome');
});

