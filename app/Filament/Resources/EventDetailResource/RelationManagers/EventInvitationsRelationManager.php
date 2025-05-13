<?php

namespace App\Filament\Resources\EventDetailResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventInvitationsRelationManager extends RelationManager
{
    protected static string $relationship = 'invitationList';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organizer_user_id')
                    ->relationship('organizer', 'name'),
                Forms\Components\Select::make('participant_user_id')
                    ->relationship('participant', 'name'),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'teamName'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('organizer.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('participant.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.teamName')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
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
}