<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SecurityLog::with('user:id,name,email');

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20);

        return response()->json($logs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This endpoint is for internal use or public reporting (e.g. CSP)
        // Validation is minimal to ensure we capture as much as possible
        
        $log = SecurityLog::create([
            'event_type' => $request->input('type', 'general_violation'),
            'description' => $request->input('description', 'Security event reported'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user('sanctum')?->id,
            'payload' => $request->all(),
        ]);

        return response()->json(['status' => 'logged'], 201);
    }
}
