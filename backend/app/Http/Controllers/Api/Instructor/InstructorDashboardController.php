<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstructorDashboardController extends Controller
{
    /**
     * Get dashboard statistics for the instructor.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is an instructor
        if (!$user->is_instructor && !$user->hasRole('instructor') && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Must be an instructor.'], 403);
        }

        $courses = $user->taughtCourses()->withCount('enrollments')->get();
        
        $totalStudents = $courses->sum('enrollments_count');
        $activeCoursesCount = $courses->where('status', 'published')->count();
        $totalRevenue = 0; // Replace with actual revenue logic if available

        return response()->json([
            'stats' => [
                'total_students' => $totalStudents,
                'active_courses' => $activeCoursesCount,
                'total_revenue' => $totalRevenue,
                'total_courses' => $courses->count()
            ],
            'recent_courses' => $courses->take(5)
        ]);
    }
}
