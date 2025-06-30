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

// Simple health check without DB dependencies
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'app' => 'VidaduAcademy',
        'environment' => 'production',
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'VidaduAcademy Backend is working!',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'courses_count' => \App\Models\Course::count(),
        'users_count' => \App\Models\User::count(),
    ]);
});

Route::get('/admin', function () {
    return redirect('/admin/login');
});
