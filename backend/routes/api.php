<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentVerificationController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

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

// Payment verification (public, no auth required)
Route::post('/payments/verify', [PaymentVerificationController::class, 'verifyPayment'])->name('payments.verify');

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
        Route::get('/course/{courseId}/status', [PaymentController::class, 'checkCoursePurchaseStatus']);
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

// Authentication routes with enhanced security
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('rate.limit:registration,3,5'); // 3 attempts per 5 minutes
    
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('rate.limit:login,5,1'); // 5 attempts per minute
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/change-password', [AuthController::class, 'changePassword'])
            ->middleware('rate.limit:password_change,3,60'); // 3 attempts per hour
    });
});

// Security reporting endpoint
Route::post('/security/violations', function (Request $request) {
    // Log CSP violations and other security events from frontend
    \Log::warning('Frontend security violation', $request->all());
    return response()->json(['status' => 'logged']);
})->middleware('rate.limit:security_reports,10,1');

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
