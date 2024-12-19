<?php

namespace App\Filament\Admin\Resources\TriviaResource\Pages;

use App\Filament\Admin\Resources\TriviaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrivia extends ViewRecord
{
    protected static string $resource = TriviaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
