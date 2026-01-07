<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Objednávky';

    protected static ?string $pluralModelLabel = 'Objednávky';

    protected static ?string $modelLabel = 'Objednávka';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informácie o objednávke')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID objednávky')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Používateľ')
                            ->disabled(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'title')
                            ->label('Kurz')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Suma')
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Stav')
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->label('Mena')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Platobné údaje')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_payment_intent_id')
                            ->label('Stripe Payment Intent')
                            ->columnSpanFull()
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_session_id')
                            ->label('Stripe Session ID')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Časové údaje')
                    ->schema([
                        Forms\Components\DateTimePicker::make('purchased_at')
                            ->label('Dátum nákupu')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('refunded_at')
                            ->label('Dátum vrátenia')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Vytvorené')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Aktualizované')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Kurz')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Suma')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'refunded' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Dátum nákupu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stav')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'view' => Pages\ViewPurchase::route('/{record}'),
        ];
    }
}
