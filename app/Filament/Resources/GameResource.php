<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Games Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Game Information')
                    ->schema([
                        Forms\Components\TextInput::make('gameTitle')
                            ->label('Game Title')
                            ->maxLength(255)
                            ->placeholder('Enter game title')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('gameIcon')
                            ->label('Game Icon')
                            ->image()
                            ->directory('game')
                            ->visibility('public')
                            ->maxSize(1048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                            ->helperText('Upload a game icon (max 2MB, JPG/PNG/GIF)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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

                Tables\Columns\ImageColumn::make('gameIcon')
                    ->label('Icon')
                    ->circular()
->defaultImageUrl(url('/assets/images/404q.png'))
 ->extraImgAttributes([
        'class' => 'border-2 border-gray-300 dark:border-gray-600',
    ])
                    ->size(60)
                    ->defaultImageUrl(url('/images/default-game-icon.png')),

                Tables\Columns\TextColumn::make('gameTitle')
                    ->label('Game Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y g:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     Tables\Filters\Filter::make('has_icon')
            //         ->label('Has Icon')
            //         ->query(fn (Builder $query): Builder => $query->whereNotNull('gameIcon')),
                
            //     Tables\Filters\Filter::make('no_icon')
            //         ->label('No Icon')
            //         ->query(fn (Builder $query): Builder => $query->whereNull('gameIcon')),
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListGames::route('/'),
            // 'create' => Pages\CreateGame::route('/create'),
            // 'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }


 
}