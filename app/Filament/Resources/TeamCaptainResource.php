<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamCaptainResource\Pages;
use App\Filament\Resources\TeamCaptainResource\RelationManagers;
use App\Models\TeamCaptain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamCaptainResource extends Resource
{
    protected static ?string $model = TeamCaptain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_member_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('teams_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_member_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teams_id')
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
            'index' => Pages\ListTeamCaptains::route('/'),
            'create' => Pages\CreateTeamCaptain::route('/create'),
            'edit' => Pages\EditTeamCaptain::route('/{record}/edit'),
        ];
    }
}
