<?php

require_once 'backend/vendor/autoload.php';

$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Purchase;
use App\Models\Enrollment;

echo "=== PAYMENT STATUS CHECK ===\n\n";

// Najnovšie purchases
echo "📋 Recent Purchases (last 10):\n";
$recentPurchases = Purchase::with('user', 'course')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentPurchases as $purchase) {
    $courseName = $purchase->course ? $purchase->course->title : 'Unknown Course';
    $userName = $purchase->user ? $purchase->user->name : 'Unknown User';
    
    echo "- ID: {$purchase->id} | {$userName} | {$courseName}\n";
    echo "  Status: {$purchase->status} | Created: {$purchase->created_at}\n";
    echo "  Stripe Session: {$purchase->stripe_session_id}\n";
    
    // Check if enrollment exists
    $enrollment = Enrollment::where('user_id', $purchase->user_id)
        ->where('course_id', $purchase->course_id)
        ->first();
    
    if ($enrollment) {
        echo "  ✅ Enrollment: Yes (ID: {$enrollment->id}, Status: {$enrollment->status})\n";
    } else {
        echo "  ❌ Enrollment: No\n";
    }
    echo "\n";
}

// Pending purchases
echo "⏳ Pending Purchases:\n";
$pendingPurchases = Purchase::where('status', 'pending')
    ->with('user', 'course')
    ->orderBy('created_at', 'desc')
    ->get();

if ($pendingPurchases->count() > 0) {
    foreach ($pendingPurchases as $purchase) {
        $courseName = $purchase->course ? $purchase->course->title : 'Unknown Course';
        $userName = $purchase->user ? $purchase->user->name : 'Unknown User';
        
        echo "- ID: {$purchase->id} | {$userName} | {$courseName}\n";
        echo "  Created: {$purchase->created_at}\n";
        echo "  Stripe Session: {$purchase->stripe_session_id}\n";
        echo "\n";
    }
} else {
    echo "✅ No pending purchases found\n\n";
}

// Purchases without enrollments
echo "🚫 Completed Purchases without Enrollments:\n";
$orphanPurchases = Purchase::where('status', 'completed')
    ->whereDoesntHave('user.enrollments', function($query) {
        $query->whereRaw('enrollments.course_id = purchases.course_id');
    })
    ->with('user', 'course')
    ->get();

if ($orphanPurchases->count() > 0) {
    foreach ($orphanPurchases as $purchase) {
        $courseName = $purchase->course ? $purchase->course->title : 'Unknown Course';
        $userName = $purchase->user ? $purchase->user->name : 'Unknown User';
        
        echo "- ID: {$purchase->id} | {$userName} | {$courseName}\n";
        echo "  Status: {$purchase->status} | Created: {$purchase->created_at}\n";
        echo "  🔥 NEEDS ENROLLMENT!\n";
        echo "\n";
    }
} else {
    echo "✅ All completed purchases have enrollments\n\n";
}

// Statistics
echo "📊 Statistics:\n";
echo "- Total Purchases: " . Purchase::count() . "\n";
echo "- Completed Purchases: " . Purchase::where('status', 'completed')->count() . "\n";
echo "- Pending Purchases: " . Purchase::where('status', 'pending')->count() . "\n";
echo "- Total Enrollments: " . Enrollment::count() . "\n";
echo "- Active Enrollments: " . Enrollment::where('status', 'active')->count() . "\n";

// Users with purchases but no enrollments
echo "\n👥 Users with completed purchases but no enrollments:\n";
$usersWithoutEnrollments = User::whereHas('purchases', function($query) {
    $query->where('status', 'completed');
})->whereDoesntHave('enrollments')->with('purchases')->get();

if ($usersWithoutEnrollments->count() > 0) {
    foreach ($usersWithoutEnrollments as $user) {
        echo "- {$user->name} ({$user->email})\n";
        echo "  Purchases: " . $user->purchases->where('status', 'completed')->count() . "\n";
    }
} else {
    echo "✅ All users with completed purchases have enrollments\n";
}
