<?php

namespace App\Filament\Admin\Resources\SongResource\Pages;

use App\Filament\Admin\Resources\SongResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSong extends EditRecord
{
    protected static string $resource = SongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
