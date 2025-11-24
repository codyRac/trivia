<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Credit;
use App\Models\Service;
use App\Models\Trivia;
use App\Models\EmojiMoviePuzzle;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function start(Request $request)
    {
        // Validate the password
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the password is correct
        if ($request->password !== '2025') {
            return response()->json([
                'message' => 'The password is incorrect.'
            ], 401); // Unauthorized status code
        }

        // Return success message
        return response()->json([
            'message' => 'Password verified successfully!'
        ], 200);

    }

    public function holding()
    {
        $credits = Credit::find(1);
        $services = Service::whereColumn('times_used', '>', 'fulfilled')->get();

        // Check trivia completion status
        $timezone = 'America/Los_Angeles';
        $todayStartLA = Carbon::today($timezone);
        $todayStartUTC = $todayStartLA->copy()->setTimezone('UTC');

        // Check if trivia has been answered today
        $triviaCompleted = Trivia::where('used', true)
            ->where('used_on', '>=', $todayStartUTC)
            ->exists();

        // Check if emoji puzzle has been answered today
        $emojiCompleted = EmojiMoviePuzzle::where('used', 1)
            ->where('updated_at', '>=', $todayStartUTC)
            ->exists();

        return Inertia::render('Holding', [
            'credits' => $credits,
            'services' => $services,
            'triviaCompleted' => $triviaCompleted,
            'emojiCompleted' => $emojiCompleted,
        ]);
    }





}
