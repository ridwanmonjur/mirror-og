<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventDetailResource\Pages;
use App\Filament\Resources\EventDetailResource\RelationManagers;
use App\Models\EventDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EventDetailResource extends Resource
{
    protected static ?string $model = EventDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventName')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('startDate'),
                Forms\Components\DatePicker::make('endDate'),
                Forms\Components\DatePicker::make('sub_action_private_date')
                    ->label('Launch Date')
                    ->native(false)
                    ->displayFormat('d/m/Y') // What user sees: 25/12/2024
                    ->formatStateUsing(function ($state) {
                        // If stored as "25/12/2024", convert to date picker format
                        return $state ? \Carbon\Carbon::createFromFormat('d/m/Y', $state)->format('Y-m-d') : null;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // Convert back to your preferred storage format
                        return $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : null;
                    }),
                Forms\Components\TimePicker::make('sub_action_public_time')
                    ->label('Launch Time')
                    ->seconds(false)
                    ->timezone('Asia/Kuala_Lumpur')
                    ->displayFormat('g:i A') // Native 12-hour format
                    ->format('H:i'), // Store as 24-hour
            
                Forms\Components\TimePicker::make('startTime')
                    ->required()
                    ->seconds(false)
                    ->timezone('Asia/Kuala_Lumpur')
                    ->label('Start Time')
                    ->displayFormat('g:i A') // Native 12-hour format
                    ->format('H:i:s'),
                
                Forms\Components\TimePicker::make('endTime')
                    ->required()
                    ->seconds(false)
                    ->timezone('Asia/Kuala_Lumpur')
                    ->label('End Time')
                    ->displayFormat('g:i A') // Native 12-hour format
                    ->format('H:i:s'),
                Forms\Components\Textarea::make('eventDescription'),
                Forms\Components\Section::make('Event Banner')
                    ->description('Image upload is only available when editing an existing item')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Placeholder::make('create_notice')
                        ->content('Please create the object first, then edit to upload an image.')
                        ->visible(fn (string $context): bool => $context === 'create'),
                        Forms\Components\FileUpload::make('eventBanner')
                            ->image()
                            ->directory('images/events') 
                            ->maxSize(5120)
                            ->deleteUploadedFileUsing(function ($file, $livewire) {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                                
                                if ($livewire->record) {
                                    $livewire->record->eventBanner = null;
                                    $livewire->record->save();
                                }
                                
                                return null;
                            })
                            ->visible(fn (string $context): bool => $context === 'edit')
                            ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                                // Custom file naming logic here
                                $oldBanner = $livewire->record->userBanner;

                                $newFilename = 'eventBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                                $directory = 'images/events';
                                
                                $path = $file->storeAs($directory, $newFilename, 'public');
                                $livewire->record->eventBanner = $path;
                                $livewire->record->save();
                                if ($oldBanner && $oldBanner !== $state) {
                                    $livewire->record->destroyTeanBanner($oldBanner);
                                }
                                return $path;
                    }),
                ]),
                
                Forms\Components\TextInput::make('eventTags')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('venue')
                    ->maxLength(255),
             
             
                Forms\Components\Select::make('user_id')
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->label('Organizer')
                    ->searchable()
                    ->relationship('user', 'name', 
                    fn ($query) => $query->where('role', 'ORGANIZER') )
                    ->required(),
                Forms\Components\Select::make('event_type_id')
                    ->relationship('type', 'eventType'),
                Forms\Components\Select::make('event_tier_id')
                    ->relationship('tier', 'eventTier'),
                Forms\Components\Select::make('event_category_id')
                    ->relationship('game', 'gameTitle'),
                Forms\Components\Toggle::make('willNotify')
                    ->required(),
            ]);
    }


    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('eventBanner')
                ->searchable(),
                Tables\Columns\TextColumn::make('eventName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.eventType')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('startDate') // or any time field
                    ->label('Start Time')
                    ->dateTime('h:i A') // 12-hour format with AM/PM
                    ->sortable()
                    ->toggleable(),
            ])
          
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Brackets')
                    ->label('Brackets')
                    ->icon('heroicon-m-squares-2x2') 
                    ->url(function (EventDetail $record) {
                        return route('admin.brackets.index', $record->id) ;
                    })
                ->openUrlInNewTab(),
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
            RelationManagers\SignupRelationManager::class,
            RelationManagers\PTransactionsRelationManager::class,
            RelationManagers\EventInvitationsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventDetails::route('/'),
            // 'create' => Pages\CreateEventDetail::route('/create'),
            'edit' => Pages\EditEventDetail::route('/{record}/edit'),
        ];
    }
}
