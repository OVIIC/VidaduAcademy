<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Purchase;
use App\Services\StripeService;

echo "Fixing Tomas Marko's purchase...\n";

// Nájdeme purchase Tomasa Marka
$purchase = Purchase::where('stripe_session_id', 'cs_test_a1TyEwAf96AFKtMEP775m157VNRHYLCel8l3yUD1PymFgBnBH2eKMJUyzo')->first();

if (!$purchase) {
    echo "Purchase not found!\n";
    exit(1);
}

echo "Found purchase: ID {$purchase->id}, User: {$purchase->user->name}, Course: {$purchase->course->title}\n";
echo "Current status: {$purchase->status}\n";

// Vytvoríme StripeService a overíme session
try {
    $stripeService = app(StripeService::class);
    
    // Overíme session v Stripe
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    $session = \Stripe\Checkout\Session::retrieve($purchase->stripe_session_id);
    
    echo "Stripe session status: {$session->status}\n";
    echo "Payment status: {$session->payment_status}\n";
    
    if ($session->payment_status === 'paid') {
        echo "Payment is paid in Stripe, updating purchase and creating enrollment...\n";
        
        // Aktualizujeme purchase
        $purchase->update([
            'status' => 'completed',
            'stripe_payment_intent_id' => $session->payment_intent
        ]);
        
        // Vytvoríme enrollment
        $enrollment = \App\Models\Enrollment::create([
            'user_id' => $purchase->user_id,
            'course_id' => $purchase->course_id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
        
        echo "✅ Purchase updated to completed\n";
        echo "✅ Enrollment created: ID {$enrollment->id}\n";
        
        // Overíme
        $purchase->refresh();
        echo "Final purchase status: {$purchase->status}\n";
        
        $userEnrollments = \App\Models\Enrollment::where('user_id', $purchase->user_id)->count();
        echo "User now has {$userEnrollments} enrollments\n";
        
    } else {
        echo "❌ Payment not completed in Stripe. Status: {$session->payment_status}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
