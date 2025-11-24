<?php

namespace App\Filament\Admin\Resources\SongResource\Pages;

use App\Filament\Admin\Resources\SongResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSong extends CreateRecord
{
    protected static string $resource = SongResource::class;
}
