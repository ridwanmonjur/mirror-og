<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FriendResource\Pages;
use App\Filament\Resources\FriendResource\RelationManagers;
use App\Models\Friend;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FriendResource extends Resource
{
    protected static ?string $model = Friend::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user1_id')
                    ->label('User 1')
                    ->relationship('user1', 'name')
                    ->required(),
                Forms\Components\Select::make('user2_id')
                ->label('User 1')    
                ->relationship('user2', 'name')
                    ->required(),
                Forms\Components\Select::make('actor_id')
                    ->required()
                    ->relationship('actor', 'name'),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('user1.name')
                    ->label('User 1')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user2.name')
                    ->label('User 2')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Actor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFriends::route('/'),
            // 'create' => Pages\CreateFriend::route('/create'),
            // 'edit' => Pages\EditFriend::route('/{record}/edit'),
        ];
    }
}
