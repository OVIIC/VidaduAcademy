<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('/featured', [CourseController::class, 'featured']);
    Route::get('/instructor/{instructor}', [CourseController::class, 'byInstructor']);
    Route::get('/{slug}', [CourseController::class, 'show']);
});

// Stripe webhook (public, no auth required)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->name('stripe.webhook');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/password', [UserController::class, 'updatePassword']);
        Route::get('/certificates', [UserController::class, 'certificates']);
        Route::get('/certificate/{enrollmentId}/download', [UserController::class, 'downloadCertificate'])->name('api.certificate.download');
        Route::get('/export-data', [UserController::class, 'exportData']);
        Route::delete('/account', [UserController::class, 'deleteAccount']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::post('/checkout', [PaymentController::class, 'createCheckoutSession']);
        Route::get('/history', [PaymentController::class, 'purchaseHistory']);
    });

    // Learning routes
    Route::prefix('learning')->group(function () {
        Route::get('/courses', [LearningController::class, 'enrolledCourses']);
        Route::get('/course/{courseSlug}', [LearningController::class, 'courseContent']);
        Route::get('/course/{courseSlug}/lesson/{lessonSlug}', [LearningController::class, 'watchLesson']);
        Route::post('/course/{courseSlug}/lesson/{lessonSlug}/progress', [LearningController::class, 'updateProgress']);
    });

    // My enrolled courses
    Route::get('/my-courses', [EnrollmentController::class, 'getMyEnrolledCourses']);
    Route::post('/enroll-me', [EnrollmentController::class, 'enrollSelf']);
});

// Authentication routes will be handled by Laravel Sanctum
Route::middleware('guest')->group(function () {
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
});

// Enrollment routes (admin/instructor only)
Route::middleware(['auth:sanctum'])->prefix('enrollments')->group(function () {
    Route::get('/user/{user}/courses', [EnrollmentController::class, 'getUserCourses']);
    Route::post('/enroll', [EnrollmentController::class, 'enrollUser']);
    Route::delete('/unenroll', [EnrollmentController::class, 'unenrollUser']);
    Route::put('/{enrollment}/progress', [EnrollmentController::class, 'updateProgress']);
    Route::get('/course/{course}/students', [EnrollmentController::class, 'getCourseEnrollments']);
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'environment' => app()->environment(),
    ]);
});
