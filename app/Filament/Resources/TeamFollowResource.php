<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamFollowResource\Pages;
use App\Filament\Resources\TeamFollowResource\RelationManagers;
use App\Models\TeamFollow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamFollowResource extends Resource
{
    protected static ?string $model = TeamFollow::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Select::make('team_id')
                    ->relationship('team', 'teamName',
                    fn ($query) => $query->whereNotNull('teamName') 
                    )
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('team.teamName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTeamFollows::route('/'),
            // 'create' => Pages\CreateTeamFollow::route('/create'),
            // 'edit' => Pages\EditTeamFollow::route('/{record}/edit'),
        ];
    }
}
