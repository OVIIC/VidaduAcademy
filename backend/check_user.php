<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Purchase;
use App\Models\Enrollment;

echo "Searching for user Tomas Marko...\n";

// Hľadáme používateľa Tomas Marko
$user = User::where('name', 'LIKE', '%Tomas%')
    ->orWhere('name', 'LIKE', '%Marko%')
    ->orWhere('email', 'LIKE', '%tomas%')
    ->orWhere('email', 'LIKE', '%marko%')
    ->first();

if (!$user) {
    echo "User Tomas Marko not found. Listing all users:\n";
    $users = User::all();
    foreach ($users as $u) {
        echo "- {$u->name} ({$u->email})\n";
    }
    echo "\nListing all purchases:\n";
    $purchases = Purchase::with('user', 'course')->get();
    foreach ($purchases as $purchase) {
        echo "- Purchase ID: {$purchase->id}, User: {$purchase->user->name} ({$purchase->user->email}), Course: {$purchase->course->title}, Status: {$purchase->status}\n";
    }
    exit;
}

echo "Found user: {$user->name} ({$user->email})\n";
echo "User ID: {$user->id}\n\n";

// Skontrolujeme purchases
$purchases = Purchase::where('user_id', $user->id)->with('course')->get();
echo "Purchases count: " . $purchases->count() . "\n";
foreach ($purchases as $purchase) {
    $courseName = $purchase->course ? $purchase->course->title : 'Unknown Course';
    echo "- Purchase ID: {$purchase->id}, Course: {$courseName} (ID: {$purchase->course_id}), Status: {$purchase->status}, Stripe Session: {$purchase->stripe_session_id}\n";
    echo "  Created: {$purchase->created_at}, Amount: {$purchase->amount}\n";
}

echo "\n";

// Skontrolujeme enrollments
$enrollments = Enrollment::where('user_id', $user->id)->with('course')->get();
echo "Enrollments count: " . $enrollments->count() . "\n";
foreach ($enrollments as $enrollment) {
    $courseName = $enrollment->course ? $enrollment->course->title : 'Unknown Course';
    echo "- Enrollment ID: {$enrollment->id}, Course: {$courseName} (ID: {$enrollment->course_id}), Status: {$enrollment->status}\n";
    echo "  Created: {$enrollment->created_at}\n";
}

// Ukážeme aj najnovšie purchases všeobecne
echo "\n\nAll recent purchases (last 10):\n";
$recentPurchases = Purchase::with('user', 'course')->orderBy('created_at', 'desc')->limit(10)->get();
foreach ($recentPurchases as $purchase) {
    $courseName = $purchase->course ? $purchase->course->title : 'Unknown Course';
    echo "- {$purchase->user->name} bought {$courseName}, Status: {$purchase->status}, Created: {$purchase->created_at}\n";
}
