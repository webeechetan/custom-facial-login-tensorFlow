<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceLoginController;
use App\Http\Controllers\FaceRegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('face-login');
});

Route::get('/register', function () {
    return view('face-register');
});

Route::post('/register-face', [FaceRegisterController::class, 'register']);

Route::post('/face-login', [FaceLoginController::class, 'login']);

