<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $exists = NewsletterSubscriber::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Tento e-mail je už zaregistrovaný na odber.'
            ], 409);
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Úspešne ste sa prihlásili na odber noviniek.'
        ], 201);
    }
}
