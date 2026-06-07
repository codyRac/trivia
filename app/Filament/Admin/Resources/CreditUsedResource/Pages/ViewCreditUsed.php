<?php

namespace App\Filament\Admin\Resources\CreditUsedResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\CreditUsedResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCreditUsed extends ViewRecord
{
    protected static string $resource = CreditUsedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
