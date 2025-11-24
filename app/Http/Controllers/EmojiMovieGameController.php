<?php

namespace App\Http\Controllers;

use App\Models\EmojiMoviePuzzle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmojiMovieGameController extends Controller
{
    public function show(Request $request)
    {
        $today = Carbon::today();

        $puzzle = EmojiMoviePuzzle::whereNull('used')->first();

        if (! $puzzle) {
            // Fallback: latest puzzle
            $puzzle = EmojiMoviePuzzle::orderByDesc('used')->first();
        }

        if (! $puzzle) {
            abort(404, 'No emoji movie puzzle found.');
        }

        $choices = $puzzle->choices();

        return view('emoji-movie.show', [
            'puzzle'  => $puzzle,
            'choices' => $choices,
        ]);
    }

    public function check(Request $request)
    {
        $puzzle = EmojiMoviePuzzle::findOrFail($request->input('puzzle_id'));

        $guess = $request->input('answer');

        $isCorrect = trim($guess) === $puzzle->correct_answer;

        return back()->with([
            'emoji_movie_result' => $isCorrect ? 'correct' : 'wrong',
            'emoji_movie_guess'  => $guess,
        ]);
    }
}
