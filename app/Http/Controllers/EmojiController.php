<?php

namespace App\Http\Controllers;

use App\Models\{EmojiMoviePuzzle, Credit};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class EmojiController extends Controller
{
    /**
     * Display the emoji puzzle page.
     */
    public function index()
    {
        $credits = Credit::find(1);

        // Check if there is a puzzle answered today
        $timezone = 'America/Los_Angeles';
        $todayStartLA = Carbon::today($timezone);
        $todayStartUTC = $todayStartLA->copy()->setTimezone('UTC');

        // Check if puzzle has already been answered today
        $hasAnsweredToday = EmojiMoviePuzzle::where('used', 1)
            ->where('updated_at', '>=', $todayStartUTC)
            ->exists();

        if ($hasAnsweredToday) {
            return Inertia::render('Emoji', [
                'credits' => $credits,
                'canAnswer' => false,
                'puzzle' => null,
            ]);
        }

        // Get a random puzzle that hasn't been used today
        $puzzle = EmojiMoviePuzzle::where(function($query) use ($todayStartUTC) {
                $query->whereNull('used')
                    ->orWhere('used', 0)
                    ->orWhere('updated_at', '<', $todayStartUTC);
            })
            ->inRandomOrder()
            ->first();

        if (!$puzzle) {
            return Inertia::render('Emoji', [
                'credits' => $credits,
                'canAnswer' => false,
                'puzzle' => null,
            ]);
        }

        // Combine correct answer and wrong answers, then shuffle
        $answers = collect([
            $puzzle->correct_answer,
            $puzzle->wrong_answer_1,
            $puzzle->wrong_answer_2,
            $puzzle->wrong_answer_3,
            $puzzle->wrong_answer_4,
            $puzzle->wrong_answer_5,
            $puzzle->wrong_answer_6,
        ])->shuffle()->values();

        return Inertia::render('Emoji', [
            'credits' => $credits,
            'canAnswer' => true,
            'puzzle' => [
                'id' => $puzzle->id,
                'emojis' => $puzzle->emojis,
                'answers' => $answers,
            ]
        ]);
    }

    /**
     * Handle the puzzle answer submission.
     */
    public function answer(Request $request)
    {
        $request->validate([
            'puzzle_id' => 'required|exists:emoji_movie_puzzles,id',
            'answer' => 'required|string',
        ]);

        $puzzle = EmojiMoviePuzzle::findOrFail($request->puzzle_id);

        // Check if the provided answer matches the correct answer
        $message = 'Wrong answer! Better luck next time!';
        $credits = Credit::find(1);

        if ($puzzle->correct_answer === $request->answer) {
            $message = '🎉 Correct! You got it right!';
            $credits->credits += 10;
            $credits->earned += 10;
            $credits->update();
        }

        // Mark puzzle as used today
        $puzzle->used = 1;
        $puzzle->update();

        return Response::json([
            'message' => $message,
            'credits' => $credits->credits,
            'earned' => $credits->earned,
            'correct_answer' => $puzzle->correct_answer,
        ], 200);
    }
}
