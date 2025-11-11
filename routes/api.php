<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\APIPaymentUploadController;

Route::post('/payments/upload', [APIPaymentUploadController::class, 'storeFile']);

