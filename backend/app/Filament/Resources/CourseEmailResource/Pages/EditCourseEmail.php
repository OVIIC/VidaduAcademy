<?php

namespace App\Filament\Resources\CourseEmailResource\Pages;

use App\Filament\Resources\CourseEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseEmail extends EditRecord
{
    protected static string $resource = CourseEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
