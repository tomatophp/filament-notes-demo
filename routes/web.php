<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('app/login');
});

Route::middleware(['web', 'throttle:10'])->group(function (){
    Route::get('/login/{provider}', [AuthController::class, 'provider'])->name('login.provider');
    Route::get('/login/{provider}/callback', [AuthController::class, 'callback'])->name('login.provider.callback');
});
