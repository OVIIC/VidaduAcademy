<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function createCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = Auth::user();

        // Check if user already has access to this course
        if ($user->hasPurchased($course)) {
            return response()->json([
                'error' => 'You already have access to this course'
            ], 422);
        }

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:3005');
            
            $sessionUrl = $this->stripeService->createCheckoutSession(
                $course,
                $user,
                $request->get('success_url', $frontendUrl . '/my-courses?payment=success'),
                $request->get('cancel_url', $frontendUrl . '/course/' . $course->slug)
            );

            return response()->json(['checkout_url' => $sessionUrl]);
        } catch (\Exception $e) {
            \Log::error('Stripe checkout session creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Unable to create checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $this->stripeService->handleWebhook($request);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Webhook processing failed'
            ], 400);
        }
    }

    public function checkCoursePurchaseStatus($courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        $hasPurchased = $user->hasPurchased($course);
        $isEnrolled = $user->isEnrolledIn($course);

        return response()->json([
            'has_purchased' => $hasPurchased,
            'is_enrolled' => $isEnrolled,
            'course_id' => $course->id,
            'course_title' => $course->title,
        ]);
    }

    public function purchaseHistory(): JsonResponse
    {
        $purchases = Auth::user()
            ->purchases()
            ->with('course')
            ->where('status', 'completed')
            ->orderBy('purchased_at', 'desc')
            ->paginate(10);

        return response()->json($purchases);
    }
}
