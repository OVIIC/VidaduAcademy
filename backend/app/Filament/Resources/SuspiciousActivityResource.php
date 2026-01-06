<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuspiciousActivityResource\Pages;
use App\Filament\Resources\SuspiciousActivityResource\RelationManagers;
use App\Models\SuspiciousActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuspiciousActivityResource extends Resource
{
    protected static ?string $model = SuspiciousActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Bezpečnosť';

    protected static ?string $navigationLabel = 'Podozrivé aktivity';

    protected static ?string $pluralModelLabel = 'Podozrivé aktivity';

    protected static ?string $modelLabel = 'Podozrivá aktivita';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detaily aktivity')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->label('Typ útoku')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Adresa')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull()
                            ->disabled(),
                        Forms\Components\TextInput::make('endpoint')
                            ->label('Endpoint')
                            ->disabled(),
                        Forms\Components\TextInput::make('method')
                            ->label('Metóda')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Analýza rizika')
                    ->schema([
                        Forms\Components\TextInput::make('risk_score')
                            ->label('Skóre rizika')
                            ->disabled(),
                        Forms\Components\Toggle::make('blocked')
                            ->label('Zablokované')
                            ->disabled(),
                        Forms\Components\KeyValue::make('headers')
                            ->label('Hlavičky požiadavky')
                            ->columnSpanFull()
                            ->disabled(),
                        Forms\Components\KeyValue::make('payload')
                            ->label('Payload')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Čas')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->searchable()
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresa')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('risk_score')
                    ->label('Riziko')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                         $state >= 8 => 'danger',
                         $state >= 5 => 'warning',
                         default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('blocked')
                    ->label('Zablokované')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-check-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('blocked')
                    ->label('Zablokované'),
                Tables\Filters\Filter::make('high_risk')
                    ->label('Vysoké riziko')
                    ->query(fn (Builder $query): Builder => $query->where('risk_score', '>=', 7)),
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
            'index' => Pages\ListSuspiciousActivities::route('/'),
            // 'create' => Pages\CreateSuspiciousActivity::route('/create'),
            // 'edit' => Pages\EditSuspiciousActivity::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
