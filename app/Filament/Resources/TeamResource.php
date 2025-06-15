<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\CountryRegion;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Io238\ISOCountries\Models\Country;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        // Make sure the countries are loaded
        $countries = [];
        
        // Get all country codes and names from the package
        foreach (CountryRegion::all() as $country) {
            $countries[$country->id] = $country->name;
        }
        
        return $form
            ->schema([
                Forms\Components\TextInput::make('teamName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('creator_id')
                    ->required()
                    ->label("Creator")
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->relationship('user', 'name', 
                    fn ($query) => $query->where('role', 'PARTICIPANT') 
                ),
                Forms\Components\TextInput::make('teamDescription')
                    ->maxLength(255),
                Forms\Components\Section::make('Event Category')
                    ->description('Image upload is only available when editing an existing item')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Placeholder::make('create_notice')
                        ->content('Please create the object first, then edit to upload an image.')
                        ->visible(fn (string $context): bool => $context === 'create'),
                        Forms\Components\FileUpload::make('teamBanner')
                            ->image()
                            ->directory('images/team') 
                            ->maxSize(5120)
                            ->deleteUploadedFileUsing(function ($file, $livewire) {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                                
                                if ($livewire->record) {
                                    $livewire->record->teamBanner = null;
                                    $livewire->record->save();
                                }
                                
                                return null;
                            })
                            ->visible(fn (string $context): bool => $context === 'edit')
                            ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                                // Custom file naming logic here
                                $oldBanner = $livewire->record->userBanner;

                                $newFilename = 'teamBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                                $directory = 'images/team';
                                
                                $path = $file->storeAs($directory, $newFilename, 'public');
                                $livewire->record->teamBanner = $path;
                                $livewire->record->save();
                                if ($oldBanner && $oldBanner !== $state) {
                                    $livewire->record->destroyTeanBanner($oldBanner);
                                }
                                return $path;
                            }),
                        ]),
                Forms\Components\Select::make('country')
                    ->label('Country')
                    ->options($countries)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $country = Country::find($state);
                            if ($country) {
                                $set('country_name', $country->name);
                                $set('country_flag', $country->flag);
                            }
                        } else {
                            $set('country_name', null);
                            $set('country_flag', null);
                        }
                    })
                    ->searchable()
                    ->placeholder('Select a country'),
                Forms\Components\Hidden::make('country_name'),
                Forms\Components\Hidden::make('country_flag'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('teamBanner'),

                Tables\Columns\TextColumn::make('teamName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Team Creator')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            RelationManagers\TeamFollowRelationManager::class,
            RelationManagers\TeamProfileRelationManager::class,
            RelationManagers\MembersRelationManager::class,
            RelationManagers\TeamCaptainRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            // 'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
