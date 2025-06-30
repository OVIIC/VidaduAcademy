<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'VidaduAcademy Backend is working!',
        'timestamp' => date('c'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
    ]);
});

Route::get('/admin', function () {
    return redirect('/admin/login');
});
