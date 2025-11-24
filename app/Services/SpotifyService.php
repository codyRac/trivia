<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    /**
     * Get a Spotify track URL for the given song.
     *
     * Uses title + artist (+ optional album) for a precise search.
     */
    public function searchSongUrl(string $title, string $artist, ?string $album = null): ?string
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return null;
        }

        // Build a more precise Spotify search query
        // Example: track:Blinding Lights artist:The Weeknd album:After Hours
        $queryParts = [
            "track:{$title}",
            "artist:{$artist}",
        ];

        if ($album) {
            $queryParts[] = "album:{$album}";
        }

        $query = implode(' ', $queryParts);

        $response = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/search', [
                'q'    => $query,
                'type' => 'track',
                'limit'=> 1,
            ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        $tracks = $data['tracks']['items'] ?? [];

        if (count($tracks) === 0) {
            return null;
        }

        $track = $tracks[0];

        // Spotify track URL is in the "external_urls" block
        return $track['external_urls']['spotify'] ?? null;
    }

    /**
     * Get and cache a Spotify API access token using Client Credentials flow.
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('spotify_access_token', 3400, function () {
            $clientId = config('services.spotify.client_id');
            $clientSecret = config('services.spotify.client_secret');

            if (! $clientId || ! $clientSecret) {
                return null;
            }

            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post('https://accounts.spotify.com/api/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return $data['access_token'] ?? null;
        });
    }
}
