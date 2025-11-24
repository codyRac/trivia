<?php

namespace App\Filament\Admin\Resources\EmojiMoviePuzzleResource\Pages;

use App\Filament\Admin\Resources\EmojiMoviePuzzleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmojiMoviePuzzle extends EditRecord
{
    protected static string $resource = EmojiMoviePuzzleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
