<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class MusicController extends Controller
{
    /**
     * Display the music page with one song.
     */
    public function index()
    {
        // Get one song - you can modify this to get a specific song or random one
        $song = Song::where('rating',0)->inRandomOrder()->first(); // Gets the first song, or use ->inRandomOrder()->first() for random

        if (!$song) {
            return Inertia::render('Music', [
                'song' => [
                    'id' => null,
                    'title' => 'No song available',
                    'artist' => '',
                    'album' => '',
                    'spotify_url' => null,
                    'apple_music_url' => null,
                ]
            ]);
        }

        return Inertia::render('Music', [
            'song' => [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'album' => $song->album,
                'spotify_url' => $song->spotify_link ?? null,
                'apple_music_url' => $song->apple_music_link ?? null,
            ]
        ]);
    }

    /**
     * Update the song rating.
     */
    public function rate(Request $request)
    {
        $request->validate([
            'song_id' => 'required|exists:songs,id',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $song = Song::findOrFail($request->song_id);

        // Update the rating
        $song->rating = $request->rating;
        $song->save();

        return Response::json([
            'message' => 'Thank you for rating this song!',
            'rating' => $song->rating,
        ], 200);
    }
}
