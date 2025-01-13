<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RosterMemberResource\Pages;
use App\Filament\Resources\RosterMemberResource\RelationManagers;
use App\Models\RosterMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RosterMemberResource extends Resource
{
    protected static ?string $model = RosterMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name',
                    fn ($query) => $query->where('role', 'PARTICIPANT')
                    )
                    ->required(),
                Forms\Components\TextInput::make('join_events_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('team_member_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('team_id')
                    ->required()
                    ->relationship('team', 'teamName',
                    fn ($query) => $query->whereNotNull('teamName')
                ),
                Forms\Components\Toggle::make('vote_to_quit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('join_events_id')
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
                Tables\Columns\TextColumn::make('team_member_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.teamName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('vote_to_quit')
                    ->boolean(),
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
            'index' => Pages\ListRosterMembers::route('/'),
            'create' => Pages\CreateRosterMember::route('/create'),
            'edit' => Pages\EditRosterMember::route('/{record}/edit'),
        ];
    }
}
