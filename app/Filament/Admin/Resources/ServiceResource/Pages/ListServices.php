<?php

namespace App\Filament\Admin\Resources\ServiceResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Widgets\{ServicesSpentRecently,Services, UnFulfilledServices};


class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Services::class,
            ServicesSpentRecently::class,
            UnFulfilledServices::class,

        ];
    }
}
