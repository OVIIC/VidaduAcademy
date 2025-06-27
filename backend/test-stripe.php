<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Stripe\Stripe;
use Stripe\Checkout\Session;

try {
    // Set Stripe API key
    Stripe::setApiKey(config('services.stripe.secret'));
    
    echo "Testing Stripe connection...\n";
    
    // Create a simple test session
    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 4999, // €49.99
                    'product_data' => [
                        'name' => 'Test Course',
                        'description' => 'Test course description',
                    ],
                ],
                'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => 'http://localhost:3005/payment/success',
        'cancel_url' => 'http://localhost:3005/courses',
    ]);
    
    echo "✅ Stripe checkout session created successfully!\n";
    echo "Session ID: " . $session->id . "\n";
    echo "Session URL: " . $session->url . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Error type: " . get_class($e) . "\n";
}
