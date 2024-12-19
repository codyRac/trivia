<?php

namespace App\Filament\Admin\Resources\TriviaResource\Pages;

use App\Filament\Admin\Resources\TriviaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrivia extends ListRecords
{
    protected static string $resource = TriviaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
