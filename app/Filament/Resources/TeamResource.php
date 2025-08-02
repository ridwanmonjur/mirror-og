<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\CountryRegion;
use App\Models\EventCategory;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use App\Filament\Traits\HandlesFilamentExceptions;

class TeamResource extends Resource
{
    use HandlesFilamentExceptions;

    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        // Make sure the countries are loaded
        $countries = [];
        $games = [];
        // Get all country codes and names from the package
        $countryColection = CountryRegion::getAllCached();
        foreach ($countryColection as $country) {
            $countries[$country->id] = $country->name;
        }

        $gameCollection = EventCategory::getAllCached();
        foreach ($gameCollection as $game) {
            if ($game && $game->gameTitle != null && $game->gameTitle !== '') {
                $games[$game->id] = $game->gameTitle;
            }
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('teamName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('creator_id')
                    ->required()
                    ->label('Creator')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->relationship('user', 'name',
                        fn ($query) => $query->where('role', 'PARTICIPANT')
                    ),
                Forms\Components\TextInput::make('teamDescription')
                    ->maxLength(255),

                Forms\Components\Select::make('all_categories_array')
                    ->label('All Games')
                    ->options($games)
                    ->multiple()
                    ->searchable()
                    ->nullable()
                    ->placeholder('Select games this team participates in')
                    ->reactive()
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // Convert pipe-separated string to array when loading
                        if ($record && $record->all_categories) {
                            $gameIds = [];
                            preg_match_all('/\|(\d+)\|/', $record->all_categories, $matches);
                            if (! empty($matches[1])) {
                                $gameIds = array_map('intval', $matches[1]);
                            }
                            $component->state($gameIds);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Store selected games and clear default game if it's not in the selected games
                        $selectedGames = is_array($state) ? $state : [];
                        $currentDefault = $get('default_category_id');

                        if ($currentDefault && ! in_array($currentDefault, $selectedGames)) {
                            $set('default_category_id', null);
                        }
                    })
                    ->dehydrated(false), // Don't save this field directly

                Forms\Components\Select::make('default_category_id')
                    ->label('Default Game')
                    ->options(function (callable $get) use ($games) {
                        $selectedGames = $get('all_categories_array');
                        $selectedGames = is_array($selectedGames) ? $selectedGames : [];

                        if (empty($selectedGames)) {
                            return [];
                        }

                        // Only show games that are selected in all_categories_array
                        return array_intersect_key($games, array_flip($selectedGames));
                    })
                    ->searchable()
                    ->nullable()
                    ->placeholder('Select a default game')
                    ->helperText('Select from games chosen above'),

                Forms\Components\Hidden::make('all_categories')
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // This will be handled by the all_categories_array field
                    })
                    ->dehydrateStateUsing(function ($state, $get) {
                        // Convert array back to pipe-separated string when saving
                        $selectedGames = $get('all_categories_array');
                        if (is_array($selectedGames) && ! empty($selectedGames)) {
                            return '|'.implode('|', $selectedGames).'|';
                        }

                        return null;
                    }),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'public' => 'Public - Players can apply to join',
                        'private' => 'Private - Only team can send invites to players',
                        'open' => 'Open - Players immediately join',
                    ])
                    ->default('open')
                    ->required(),

                Forms\Components\TextInput::make('member_limit')
                    ->label('Member Limit')
                    ->numeric()
                    ->default(10)
                    ->minValue(1)
                    ->maxValue(100)
                    ->required(),

                Forms\Components\Section::make('Team Banner')
                    ->description('Image upload is only available when editing an existing category ')
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

                                $newFilename = 'teamBanner-'.time().'-'.auth()->id().'.'.$file->getClientOriginalExtension();
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
                    ->label('Region')
                    ->options($countries)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                        if ($state) {
                            $country = CountryRegion::find($state);
                            if ($country) {
                                $set('country_name', $country['name']);
                                $set('country_flag', $country['emoji_flag']);
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
                Tables\Columns\ImageColumn::make('teamBanner')
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/404q.png'))
                    ->extraImgAttributes([
                        'class' => 'border border-gray-300 dark:border-gray-600',
                    ])
                    ->size(60),
                Tables\Columns\TextColumn::make('teamName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Team Creator')
                    ->numeric(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'danger',
                        'open' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('member_limit')
                    ->label('Member Limit')
                    ->numeric(),

                Tables\Columns\TextColumn::make('country_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),

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
            RelationManagers\TeamFollowRelationManager::class,
            RelationManagers\TeamProfileRelationManager::class,
            RelationManagers\MembersRelationManager::class,
            RelationManagers\TeamCaptainRelationManager::class,
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
