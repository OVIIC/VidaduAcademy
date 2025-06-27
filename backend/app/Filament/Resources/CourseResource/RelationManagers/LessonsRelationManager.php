<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Základné informácie')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Názov lekcie')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', \Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL identifikátor')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('Poradie')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->label('Stav')
                                    ->options([
                                        'draft' => 'Koncept',
                                        'published' => 'Publikované',
                                        'archived' => 'Archivované',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Krátky popis')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Toggle::make('is_free')
                            ->label('Bezplatná lekcia')
                            ->helperText('Bezplatné lekcie môžu vidieť aj neregistrovaní používatelia')
                            ->default(false),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Video obsah')
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->maxLength(500)
                            ->helperText('URL adresa videa (YouTube, Vimeo, alebo priamy odkaz)'),
                        Forms\Components\TextInput::make('video_duration')
                            ->label('Trvanie videa (sekundy)')
                            ->numeric()
                            ->default(0)
                            ->helperText('Zadajte trvanie v sekundách (napr. 300 pre 5 minút)'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Obsah lekcie')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Obsah lekcie')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->helperText('Sem môžete pridať textový obsah, obrázky, odkazy a ďalšie informácie k lekcii'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Prepis videa')
                    ->schema([
                        Forms\Components\Textarea::make('transcript')
                            ->label('Prepis videa')
                            ->rows(8)
                            ->helperText('Textový prepis video obsahu pre lepšiu dostupnosť a SEO'),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Forms\Components\Section::make('Dodatočné zdroje')
                    ->schema([
                        Forms\Components\Repeater::make('resources')
                            ->label('Súbory a odkazy')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Názov')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\Select::make('type')
                                    ->label('Typ')
                                    ->options([
                                        'pdf' => 'PDF dokument',
                                        'link' => 'Webový odkaz',
                                        'download' => 'Súbor na stiahnutie',
                                        'video' => 'Video',
                                        'image' => 'Obrázok',
                                        'document' => 'Dokument',
                                        'external' => 'Externý zdroj',
                                    ])
                                    ->default('link')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL adresa')
                                    ->url()
                                    ->required()
                                    ->columnSpan(3),
                                Forms\Components\Textarea::make('description')
                                    ->label('Popis')
                                    ->rows(2)
                                    ->columnSpan(3),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Pridať zdroj')
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Poradie')
                    ->sortable()
                    ->width('80px'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Názov lekcie')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => \Str::limit(strip_tags($record->description), 60)),
                Tables\Columns\TextColumn::make('content')
                    ->label('Obsah')
                    ->html()
                    ->limit(100)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('video_duration')
                    ->label('Trvanie')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('i:s', $state) : 'N/A')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_free')
                    ->label('Bezplatná')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('resources')
                    ->label('Zdroje')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' zdroj(ov)' : '0 zdrojov')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Koncept',
                        'published' => 'Publikované',
                        'archived' => 'Archivované',
                    ]),
                Tables\Filters\TernaryFilter::make('is_free')
                    ->label('Bezplatná lekcia'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Pridať lekciu')
                    ->icon('heroicon-o-plus'),
                Tables\Actions\Action::make('import_lessons')
                    ->label('Importovať lekcie')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('CSV súbor')
                            ->acceptedFileTypes(['text/csv', '.csv'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Import functionality would go here
                        $this->notify('success', 'Import funkcia bude implementovaná v budúcej verzii');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Zobraziť'),
                Tables\Actions\EditAction::make()
                    ->label('Upraviť'),
                Tables\Actions\ReplicateAction::make()
                    ->label('Duplikovať')
                    ->excludeAttributes(['slug'])
                    ->beforeReplicaSaved(function (Model $replica): void {
                        $replica->slug = $replica->slug . '-copy';
                        $replica->title = $replica->title . ' (Kópia)';
                        $replica->status = 'draft';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Odstrániť'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikovať vybrané')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'published']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('draft')
                        ->label('Nastaviť ako koncept')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'draft']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }
}
