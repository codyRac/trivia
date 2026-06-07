<?php

namespace App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\EmojiMoviePuzzleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmojiMoviePuzzles extends ListRecords
{
    protected static string $resource = EmojiMoviePuzzleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
