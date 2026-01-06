<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CoursePopularityWidget extends BaseWidget
{
    protected static ?string $heading = 'Najpopulárnejšie kurzy';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Course::query()
                    ->withCount('enrollments')
                    ->orderBy('enrollments_count', 'desc')
                    ->take(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Názov kurzu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('Inštruktor'),
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Počet zápisov')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Cena')
                    ->money('EUR'),
            ])
            ->paginated(false);
    }
}
