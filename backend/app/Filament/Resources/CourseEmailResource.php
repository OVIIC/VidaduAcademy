<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseEmailResource\Pages;
use App\Filament\Resources\CourseEmailResource\RelationManagers;
use App\Models\CourseEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseEmailResource extends Resource
{
    protected static ?string $model = CourseEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $modelLabel = 'Emailová adresa z kurzov';

    protected static ?string $pluralModelLabel = 'Emailové adresy z kurzov';

    protected static ?string $navigationLabel = 'Emailové adresy z kurzov';

    protected static ?string $navigationGroup = 'Správa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'title')
                    ->nullable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Kurz')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseEmails::route('/'),
            'create' => Pages\CreateCourseEmail::route('/create'),
            'edit' => Pages\EditCourseEmail::route('/{record}/edit'),
        ];
    }
}
