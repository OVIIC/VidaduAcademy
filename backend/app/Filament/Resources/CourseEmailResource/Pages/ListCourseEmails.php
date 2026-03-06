<?php

namespace App\Filament\Resources\CourseEmailResource\Pages;

use App\Filament\Resources\CourseEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseEmails extends ListRecords
{
    protected static string $resource = CourseEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
