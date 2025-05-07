<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventInvitationResource\Pages;
use App\Filament\Resources\EventInvitationResource\RelationManagers;
use App\Models\EventInvitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventInvitationResource extends Resource
{
    protected static ?string $model = EventInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organizer_user_id')
                    ->relationship('organizer', 'name'),
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'eventName', fn ($query) => $query->whereNotNull('eventName')),
              
                Forms\Components\Select::make('participant_user_id')
                    ->relationship('participant', 'name'),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'teamName'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('organizer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.eventName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participant.name')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('team.teamName')
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
            'index' => Pages\ListEventInvitations::route('/'),
            // 'create' => Pages\CreateEventInvitation::route('/create'),
            // 'edit' => Pages\EditEventInvitation::route('/{record}/edit'),
        ];
    }
}
