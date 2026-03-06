<?php

namespace App\Http\Controllers;

use App\Models\CourseEmail;
use Illuminate\Http\Request;

class CourseEmailController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'course_id' => 'nullable|exists:courses,id'
        ]);

        $courseEmail = CourseEmail::create($validated);

        return response()->json([
            'message' => 'Email bol úspešne uložený.',
            'data' => $courseEmail
        ], 201);
    }
}
