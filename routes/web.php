<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login',[LoginController::class,'login'])->name('login');
Route::post('do-login',[LoginController::class,'doLogin'])->name('do.login');
Route::get('logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function(){
    Route::get('dashboard',[DashboardController::class,'view'])->name('dashboard');


});


