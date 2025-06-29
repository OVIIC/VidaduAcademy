#!/bin/bash

echo "🚀 MANUAL COURSE ENROLLMENT FIX"
echo "==============================="
echo "This script will manually enroll users in courses they have purchased"
echo ""

cd /Users/hermes/Documents/GitHub/VidaduAcademy/backend

echo "1. Current status check:"
echo "📊 Purchases:"
./vendor/bin/sail artisan tinker --execute="
App\Models\Purchase::all(['id', 'user_id', 'course_id', 'status'])->each(function(\$p) { 
    echo \"Purchase {\$p->id}: User {\$p->user_id} → Course {\$p->course_id} ({\$p->status})\n\"; 
});
"

echo ""
echo "📚 Enrollments:"
./vendor/bin/sail artisan tinker --execute="
App\Models\Enrollment::all(['user_id', 'course_id'])->each(function(\$e) { 
    echo \"Enrollment: User {\$e->user_id} → Course {\$e->course_id}\n\"; 
});
"

echo ""
echo "2. Enrolling users for all completed purchases:"
./vendor/bin/sail artisan tinker --execute="
\$completedPurchases = App\Models\Purchase::where('status', 'completed')->get();
echo \"Found \" . \$completedPurchases->count() . \" completed purchases\n\";

foreach (\$completedPurchases as \$purchase) {
    \$user = \$purchase->user;
    \$course = \$purchase->course;
    
    if (!\$user) {
        echo \"❌ User not found for purchase {\$purchase->id}\n\";
        continue;
    }
    
    if (!\$course) {
        echo \"❌ Course not found for purchase {\$purchase->id}\n\";
        continue;
    }
    
    if (\$user->isEnrolledIn(\$course)) {
        echo \"ℹ️  User {\$user->id} ({\$user->email}) already enrolled in '{\$course->title}'\n\";
    } else {
        \$enrollment = \$user->enrollments()->create([
            'course_id' => \$course->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);
        echo \"✅ User {\$user->id} ({\$user->email}) enrolled in '{\$course->title}'\n\";
    }
}
"

echo ""
echo "3. Final verification:"
echo "📚 All enrollments after fix:"
./vendor/bin/sail artisan tinker --execute="
\$enrollments = App\Models\Enrollment::with(['user', 'course'])->get();
echo \"Total enrollments: \" . \$enrollments->count() . \"\n\";
foreach (\$enrollments as \$enrollment) {
    \$user = \$enrollment->user;
    \$course = \$enrollment->course;
    echo \"✅ {\$user->email} enrolled in '{\$course->title}'\n\";
}
"

echo ""
echo "🎉 ENROLLMENT FIX COMPLETE!"
echo "=========================="
echo "Users should now see their purchased courses in 'Moje kurzy'"
echo ""
echo "💡 To test:"
echo "1. Open http://localhost:3001/my-courses"
echo "2. Check if purchased courses are visible"
echo "3. Try accessing course content"

cd /Users/hermes/Documents/GitHub/VidaduAcademy
