#!/bin/bash

echo "🔧 FIXING PENDING PURCHASE & ENROLLMENT"
echo "======================================="
echo "Manually completing pending purchases and creating enrollments"
echo ""

cd /Users/hermes/Documents/GitHub/VidaduAcademy/backend

echo "1. Checking current purchase status:"
./vendor/bin/sail artisan tinker --execute="
App\Models\Purchase::all(['id', 'user_id', 'course_id', 'status'])->each(function(\$p) { 
    echo \"Purchase ID: {\$p->id}, User: {\$p->user_id}, Course: {\$p->course_id}, Status: {\$p->status}\n\"; 
});
"

echo ""
echo "2. Checking current enrollments:"
./vendor/bin/sail artisan tinker --execute="
echo 'Total enrollments: ' . App\Models\Enrollment::count() . \"\n\";
App\Models\Enrollment::all(['user_id', 'course_id'])->each(function(\$e) { 
    echo \"Enrollment - User: {\$e->user_id}, Course: {\$e->course_id}\n\"; 
});
"

echo ""
echo "3. Fixing pending purchases (marking as completed):"
./vendor/bin/sail artisan tinker --execute="
\$pendingPurchases = App\Models\Purchase::where('status', 'pending')->get();
foreach (\$pendingPurchases as \$purchase) {
    \$purchase->update([
        'status' => 'completed',
        'purchased_at' => now()
    ]);
    echo \"✅ Purchase {\$purchase->id} marked as completed\n\";
}
"

echo ""
echo "4. Creating missing enrollments:"
./vendor/bin/sail artisan tinker --execute="
\$completedPurchases = App\Models\Purchase::where('status', 'completed')->get();
foreach (\$completedPurchases as \$purchase) {
    \$user = \$purchase->user;
    \$course = \$purchase->course;
    
    if (!\$user->isEnrolledIn(\$course)) {
        \$user->enrollments()->create([
            'course_id' => \$course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);
        echo \"✅ User {\$user->id} enrolled in Course {\$course->id}\n\";
    } else {
        echo \"ℹ️  User {\$user->id} already enrolled in Course {\$course->id}\n\";
    }
}
"

echo ""
echo "5. Final verification:"
echo "📊 Purchase status after fix:"
./vendor/bin/sail artisan tinker --execute="
App\Models\Purchase::all(['id', 'user_id', 'course_id', 'status'])->each(function(\$p) { 
    echo \"Purchase ID: {\$p->id}, User: {\$p->user_id}, Course: {\$p->course_id}, Status: {\$p->status}\n\"; 
});
"

echo ""
echo "📚 Enrollments after fix:"
./vendor/bin/sail artisan tinker --execute="
echo 'Total enrollments: ' . App\Models\Enrollment::count() . \"\n\";
App\Models\Enrollment::all(['user_id', 'course_id'])->each(function(\$e) { 
    echo \"Enrollment - User: {\$e->user_id}, Course: {\$e->course_id}\n\"; 
});
"

echo ""
echo "🎉 PURCHASE & ENROLLMENT FIX COMPLETE!"
echo "======================================"
echo "✅ All pending purchases marked as completed"
echo "✅ Missing enrollments created"
echo "✅ Users should now see their courses in 'Moje kurzy'"
echo ""
echo "💡 Next steps:"
echo "1. Refresh frontend to see courses in 'My Courses'"
echo "2. Test payment verification endpoint"
echo "3. Future payments will auto-enroll via verification API"
