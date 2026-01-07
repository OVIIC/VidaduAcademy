<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityLogResource\Pages;
use App\Filament\Resources\SecurityLogResource\RelationManagers;
use App\Models\SecurityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SecurityLogResource extends Resource
{
    protected static ?string $model = SecurityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Bezpečnosť';

    protected static ?string $navigationLabel = 'Bezpečnostné záznamy';

    protected static ?string $pluralModelLabel = 'Bezpečnostné záznamy';

    protected static ?string $modelLabel = 'Bezpečnostný záznam';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detaily záznamu')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Dátum a čas')
                            ->disabled(),
                        Forms\Components\TextInput::make('event_type')
                            ->label('Typ udalosti')
                            ->disabled(),
                        Forms\Components\TextInput::make('severity')
                            ->label('Závažnosť')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Adresa')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Používateľ')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Dáta')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Poznámky')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dátum')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Neregistrovaný'),
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Udalosť')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresa')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Závažnosť')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        'info' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->label('Závažnosť')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                    ]),
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('Typ udalosti')
                    ->options(fn () => SecurityLog::query()->distinct()->pluck('event_type', 'event_type')->all()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSecurityLogs::route('/'),
            // 'create' => Pages\CreateSecurityLog::route('/create'),
            // 'edit' => Pages\EditSecurityLog::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
