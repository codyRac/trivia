<?php

namespace App\Services;

use App\Models\Song;
use App\Services\AppleMusicService;
use App\Services\SpotifyService;

class SongImportService
{
    /**
     * Import songs from a CSV file.
     *
     * Expects a header row like:
     * title,artist,album,year,genre,rating,spotify_id,apple_music_id
     *
     * @return int Number of songs created
     */
    public function importFromCsv(string $path): int
    {
        if (! file_exists($path)) {
            throw new \RuntimeException("CSV file not found at path: {$path}");
        }

        $handle = fopen($path, 'r');

        if (! $handle) {
            throw new \RuntimeException("Unable to open CSV file: {$path}");
        }

        $header = fgetcsv($handle, 0, ',');

        if (! $header) {
            fclose($handle);
            throw new \RuntimeException('CSV appears to be empty or invalid (no header row).');
        }

        $created = 0;
        $apple = new AppleMusicService();
        $spotify = new SpotifyService();

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // Skip completely empty lines
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $rowData = @array_combine($header, $row);

            if (! $rowData) {
                // header/row mismatch – you can log or just skip
                continue;
            }

            $title  = trim($rowData['title'] ?? '');
            $artist = trim($rowData['artist'] ?? '');
            $album  = trim($rowData['album'] ?? '');


            if ($title === '') {
                // require at least a title
                continue;
            }
            $appleUrl = $apple->searchSongUrl($title, $artist, $album);
            $spotifyUrl = $spotify->searchSongUrl($title, $artist, $album);


            Song::create([
                'title'           => $title,
                'artist'          => $artist,
                'album'           => $album,
                'year'            => isset($rowData['year']) ? (int) $rowData['year'] : null,
                'genre'           => trim($rowData['genre'] ?? ''),
                'rating'          => isset($rowData['rating']) ? (int) $rowData['rating'] : null,
                'spotify_link'     => $spotifyUrl,
                'apple_music_link' => $appleUrl ?: null,
            ]);

            $created++;
        }

        fclose($handle);

        return $created;
    }
}
