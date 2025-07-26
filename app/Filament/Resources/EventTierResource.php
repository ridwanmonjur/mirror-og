<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventTierResource\Pages;
use App\Filament\Resources\EventTierResource\RelationManagers;
use App\Filament\Traits\HandlesFilamentExceptions;
use App\Models\EventTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EventTierResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = EventTier::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventTier')
                    ->maxLength(255),
                Forms\Components\Section::make('Event Tier')
                    ->description('Tier upload is only available when editing an existing item')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Placeholder::make('create_notice')
                        ->content('Please create the object first, then edit to upload an image.')
                        ->visible(fn (string $context): bool => $context === 'create'),
                        Forms\Components\FileUpload::make('tierIcon')
                            ->image()
                            ->directory('images/event_details') 
                            ->maxSize(5120)
                            ->visible(fn (string $context): bool => $context === 'edit')
                            ->deleteUploadedFileUsing(function ($file, $livewire) {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                                
                                if ($livewire->record) {
                                    $livewire->record->tierIcon = null;
                                    $livewire->record->save();
                                }
                                
                                return null;
                            })
                            ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                                // Generate new filename regardless of creation or edit
                                    $newFilename = 'event_details-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                                    $directory = 'images/event_details';
                                    $path = $file->storeAs($directory, $newFilename, 'public');
                                    
                                    
                                    if ($livewire->record) {
                                        $oldBanner = $livewire->record->tierIcon;
                                        $livewire->record->tierIcon = $path;
                                        $livewire->record->save();
                                        
                                        if ($oldBanner && $oldBanner !== $state) {
                                            Storage::disk('public')->delete($oldBanner);
                                        }
                                    }
                                    
                                    // Always return the path so it can be stored in the form data
                                    return $path;

                            }),
                ]),
              
                Forms\Components\TextInput::make('tierTeamSlot')
                    ->numeric()
                    ->rules(['integer', 'min:1'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('tierPrizePool')
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tierEntryFee')
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('first_setup_guide')
                    ->label('Must Configure Deadline for Each Type')
                    ->icon('heroicon-m-information-circle')
                    ->color('warning')
                    ->modalDescription('⚠️ IMPORTANT: You must configure registration open, close & early deadline durations for all event types. Without it, registration & event updates will not progress smoothly.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Got it'),
                
                Tables\Actions\Action::make('second_setup_guide')
                    ->label('Must Configure Prize for Each Position')
                    ->icon('heroicon-m-information-circle')
                    ->color('success')
                    ->modalDescription('⚠️ IMPORTANT: You must configure the prize sum for each position. Without it, the system cannot allocate rewards to the players\' wallets.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Got it'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('eventTier')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('tierIcon')
                    ->circular()
->defaultImageUrl(url('/assets/images/404q.png'))
 ->extraImgAttributes([
        'class' => 'border border-gray-300 dark:border-gray-600',
    ])
                    ->size(60),
                Tables\Columns\TextColumn::make('tierTeamSlot'),
                Tables\Columns\TextColumn::make('tierPrizePool'),
                Tables\Columns\TextColumn::make('tierEntryFee'),
            ])
            // ->filters([
            //     //
            // ])
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
            RelationManagers\TierSignupRelationManager::class,
            RelationManagers\PrizesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventTiers::route('/'),
            // 'create' => Pages\CreateEventTier::route('/create'),
            'edit' => Pages\EditEventTier::route('/{record}/edit'),
        ];
    }
}
