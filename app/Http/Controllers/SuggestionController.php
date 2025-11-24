<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
        ]);

        Suggestion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your suggestion!',
        ], 200);
    }
}
