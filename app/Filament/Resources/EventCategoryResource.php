<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventCategoryResource\Pages;
use App\Models\EventCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use App\Filament\Traits\HandlesFilamentExceptions;

class EventCategoryResource extends Resource
{
    use HandlesFilamentExceptions;

    protected static ?string $model = EventCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('gameTitle')
                    ->maxLength(255),

                Forms\Components\Section::make('Event Category')
                    ->description('Image upload is only available when editing an existing category ')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Placeholder::make('create_notice')
                            ->content('Please create the object first, then edit to upload an image.')
                            ->visible(fn (string $context): bool => $context === 'create'),
                        Forms\Components\FileUpload::make('gameIcon')
                            ->image()
                            ->directory('images/event_details')
                            ->maxSize(5120)
                            ->visible(fn (string $context): bool => $context === 'edit')
                            ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                                // Generate new filename
                                $newFilename = 'event_details-'.time().'-'.auth()->id().'.'.$file->getClientOriginalExtension();
                                $directory = 'images/event_details';
                                $path = $file->storeAs($directory, $newFilename, 'public');

                                // Access record - handle both table modal and edit page contexts
                                $recordId = $livewire->mountedTableActionRecord ?? $livewire->record?->id;
                                if ($recordId) {
                                    $record = \App\Models\EventCategory::find($recordId);

                                    if ($record) {
                                        $oldBanner = $record->gameIcon;
                                        $record->gameIcon = $path;
                                        $record->save();

                                        if ($oldBanner && $oldBanner !== $state) {
                                            Storage::disk('public')->delete($oldBanner);
                                        }
                                    }
                                }

                                return $path;
                            })
                            ->deleteUploadedFileUsing(function ($file, callable $set, $livewire) {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }

                                $recordId = $livewire->mountedTableActionRecord ?? $livewire->record?->id;
                                if ($recordId) {
                                    $record = \App\Models\EventCategory::find($recordId);

                                    if ($record) {
                                        $record->gameIcon = null;
                                        $record->save();
                                    }
                                }

                                return null;
                            }),
                    ]),
                Forms\Components\TextInput::make('eventDefinitions')
                    ->maxLength(255),
                Forms\Components\TextInput::make('player_per_team')
                    ->numeric()
                    ->default(5)
                    ->minValue(1)
                    ->maxValue(50)
                    ->label('Players per Team'),
                Forms\Components\TextInput::make('games_per_match')
                    ->numeric()
                    ->default(3)
                    ->minValue(1)
                    ->maxValue(20)
                    ->label('Games per Match'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('gameIcon')
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/404q.png'))
                    ->extraImgAttributes([
                        'class' => 'border border-gray-300 dark:border-gray-600',
                    ])
                    ->size(60),
                Tables\Columns\TextColumn::make('gameTitle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('player_per_team')
                    ->label('Players/Team')
                    ->sortable(),
                Tables\Columns\TextColumn::make('games_per_match')
                    ->label('Games/Match')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->dateTime('M d, Y — h:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->dateTime('M d, Y — h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     //
            // ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        EventCategory::clearCache();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        EventCategory::clearCache();
                    }),
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
            'index' => Pages\ListEventCategories::route('/'),
            'create' => Pages\CreateEventCategory::route('/create'),
            'edit' => Pages\EditEventCategory::route('/{record}/edit'),
        ];
    }
}
