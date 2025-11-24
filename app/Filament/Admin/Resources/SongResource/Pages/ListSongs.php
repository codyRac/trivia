<?php

namespace App\Filament\Admin\Resources\SongResource\Pages;

use App\Filament\Admin\Resources\SongResource;
use App\Filament\Admin\Widgets\HighRatedSongsCount;
use App\Filament\Admin\Widgets\RatingsDistributionChart;
use App\Filament\Admin\Widgets\AverageRatingByGenreChart;
use App\Filament\Admin\Widgets\TopArtistsBySongCount;
use App\Filament\Admin\Widgets\TopArtistsByAverageRating;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSongs extends ListRecords
{
    protected static string $resource = SongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HighRatedSongsCount::class,
            RatingsDistributionChart::class,
            AverageRatingByGenreChart::class,
            TopArtistsBySongCount::class,
            TopArtistsByAverageRating::class,
        ];
    }
}
