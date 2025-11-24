<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AppleMusicService
{
    public function searchSongUrl(string $title, string $artist, string $album = null): ?string
    {
        // Build the search phrase
        $term = trim("{$title} {$artist} {$album}");

        $response = Http::get("https://itunes.apple.com/search", [
            'term'   => $term,
            'entity' => 'song',
            'limit'  => 1,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        if (($data['resultCount'] ?? 0) === 0) {
            return null;
        }

        // Apple "trackViewUrl" → public link
        return $data['results'][0]['trackViewUrl'] ?? null;
    }
}
