<?php

namespace App\Filament\Admin\Resources\CreditResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\CreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCredit extends ViewRecord
{
    protected static string $resource = CreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
