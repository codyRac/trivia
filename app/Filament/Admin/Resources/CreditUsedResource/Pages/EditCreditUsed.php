<?php

namespace App\Filament\Admin\Resources\CreditUsedResource\Pages;

use App\Filament\Admin\Resources\CreditUsedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditUsed extends EditRecord
{
    protected static string $resource = CreditUsedResource::class;

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
