<?php

require_once 'backend/vendor/autoload.php';

$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Purchase;
use App\Models\Enrollment;

echo "=== FIXING PENDING PAYMENTS ===\n\n";

// Set Stripe API key
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));

// Find pending purchases
$pendingPurchases = Purchase::where('status', 'pending')
    ->whereNotNull('stripe_session_id')
    ->with(['user', 'course'])
    ->get();

echo "Found {$pendingPurchases->count()} pending purchases\n\n";

foreach ($pendingPurchases as $purchase) {
    echo "=== Purchase ID: {$purchase->id} ===\n";
    echo "User: {$purchase->user->name} ({$purchase->user->email})\n";
    echo "Course: {$purchase->course->title}\n";
    echo "Stripe Session: {$purchase->stripe_session_id}\n";
    echo "Created: {$purchase->created_at}\n";
    
    try {
        // Check Stripe session
        $session = \Stripe\Checkout\Session::retrieve($purchase->stripe_session_id);
        
        echo "Stripe Status: {$session->status}\n";
        echo "Payment Status: {$session->payment_status}\n";
        
        if ($session->payment_status === 'paid') {
            echo "✅ Payment is PAID in Stripe - fixing...\n";
            
            // Update purchase
            $purchase->update([
                'status' => 'completed',
                'stripe_payment_intent_id' => $session->payment_intent,
                'purchased_at' => now(),
            ]);
            echo "✅ Purchase updated to completed\n";
            
            // Create enrollment
            $existingEnrollment = Enrollment::where('user_id', $purchase->user_id)
                ->where('course_id', $purchase->course_id)
                ->first();
                
            if ($existingEnrollment) {
                echo "✅ Enrollment already exists (ID: {$existingEnrollment->id})\n";
            } else {
                $enrollment = Enrollment::create([
                    'user_id' => $purchase->user_id,
                    'course_id' => $purchase->course_id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
                echo "✅ New enrollment created (ID: {$enrollment->id})\n";
            }
            
        } elseif ($session->payment_status === 'unpaid') {
            echo "❌ Payment is UNPAID in Stripe\n";
            
            // Check how old the purchase is
            $hoursAgo = now()->diffInHours($purchase->created_at);
            if ($hoursAgo > 2) {
                echo "⚠️  Purchase is {$hoursAgo} hours old - consider canceling\n";
            }
            
        } else {
            echo "⚠️  Unknown payment status: {$session->payment_status}\n";
        }
        
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        echo "❌ Stripe Error: Session not found or invalid\n";
        echo "Error: {$e->getMessage()}\n";
        
        // If session doesn't exist in Stripe, mark as failed
        $hoursAgo = now()->diffInHours($purchase->created_at);
        if ($hoursAgo > 1) {
            echo "⚠️  Purchase is {$hoursAgo} hours old and session invalid - marking as failed\n";
            $purchase->update(['status' => 'failed']);
        }
        
    } catch (\Exception $e) {
        echo "❌ General Error: {$e->getMessage()}\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== SUMMARY ===\n";
echo "Total pending purchases processed: {$pendingPurchases->count()}\n";

// Show updated stats
$completedCount = Purchase::where('status', 'completed')->count();
$pendingCount = Purchase::where('status', 'pending')->count();
$enrollmentCount = Enrollment::count();

echo "Updated stats:\n";
echo "- Completed purchases: {$completedCount}\n";
echo "- Pending purchases: {$pendingCount}\n";
echo "- Total enrollments: {$enrollmentCount}\n";
