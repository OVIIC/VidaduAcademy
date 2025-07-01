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

// Temporary debug route - remove after fixing
Route::get('/debug-db', function () {
    ob_start();
    include base_path('debug_database.php');
    $output = ob_get_clean();
    return response($output)->header('Content-Type', 'text/plain');
});

// Standalone debug route - no Laravel dependencies
Route::get('/debug-standalone', function () {
    ob_start();
    include base_path('standalone_debug.php');
    $output = ob_get_clean();
    return response($output)->header('Content-Type', 'text/plain');
});

Route::get('/admin', function () {
    return redirect('/admin/login');
});
