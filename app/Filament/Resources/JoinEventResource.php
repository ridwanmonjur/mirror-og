<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JoinEventResource\Pages;
use App\Filament\Resources\JoinEventResource\RelationManagers;
use App\Models\JoinEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JoinEventResource extends Resource
{
    protected static ?string $model = JoinEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_details_id')
                    ->relationship('eventDetails', 'id')
                    ->required(),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'id')
                    ->required(),
                Forms\Components\TextInput::make('joiner_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('joiner_participant_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\TextInput::make('join_status')
                    ->required(),
                Forms\Components\Select::make('vote_starter_id')
                    ->relationship('voteStarter', 'name')
                    ->required(),
                Forms\Components\Toggle::make('vote_ongoing'),
                Forms\Components\TextInput::make('roster_captain_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eventDetails.id')
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
                Tables\Columns\TextColumn::make('team.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('joiner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('joiner_participant_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status'),
                Tables\Columns\TextColumn::make('join_status'),
                Tables\Columns\TextColumn::make('voteStarter.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('vote_ongoing')
                    ->boolean(),
                Tables\Columns\TextColumn::make('roster_captain_id')
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
            'index' => Pages\ListJoinEvents::route('/'),
            'create' => Pages\CreateJoinEvent::route('/create'),
            'edit' => Pages\EditJoinEvent::route('/{record}/edit'),
        ];
    }
}
