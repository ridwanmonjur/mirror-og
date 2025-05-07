<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantFollowResource\Pages;
use App\Filament\Resources\ParticipantFollowResource\RelationManagers;
use App\Models\ParticipantFollow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParticipantFollowResource extends Resource
{
    protected static ?string $model = ParticipantFollow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('participant_follower')
                    ->relationship('followerUser', 'name',
                    fn ($query) => $query->where('role', 'PARTICIPANT')  
                    )
                    ->required(),
                Forms\Components\Select::make('participant_followee')
                    ->required()
                    ->relationship('followeeUser', 'name',
                        fn ($query) => $query->where('role', 'PARTICIPANT')  
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('followerUser.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('followeeUser.name')
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
            'index' => Pages\ListParticipantFollows::route('/'),
            // 'create' => Pages\CreateParticipantFollow::route('/create'),
            // 'edit' => Pages\EditParticipantFollow::route('/{record}/edit'),
        ];
    }
}
