<?php

namespace App\Filament\Admin\Resources\TriviaResource\Pages;

use App\Filament\Admin\Resources\TriviaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrivia extends EditRecord
{
    protected static string $resource = TriviaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
