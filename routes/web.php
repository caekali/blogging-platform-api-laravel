<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GmailController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/google/auth', [GmailController::class, 'googleAuth'])->name('google.auth');
Route::get('/google/callback', [GmailController::class, 'handleOAuthRedirect']);
Route::get('/send-email', [GmailController::class, 'sendEmail'])->name('email.send');
