<?php

namespace App\Filament\Resources\SuspiciousActivityResource\Pages;

use App\Filament\Resources\SuspiciousActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuspiciousActivities extends ListRecords
{
    protected static string $resource = SuspiciousActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
