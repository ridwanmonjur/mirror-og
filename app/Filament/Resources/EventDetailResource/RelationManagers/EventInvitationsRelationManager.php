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
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->label('Organizer')
                    ->searchable()
                    ->relationship('organizer', 'name', 
                    fn ($query) => $query->where('role', 'ORGANIZER') ),
          
                Forms\Components\Select::make('team_id')
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->searchable()
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
                Tables\Columns\TextColumn::make('team.teamName')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     //
            // ])
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