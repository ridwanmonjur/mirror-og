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
                Forms\Components\TextInput::make('organizer_user_id')
                    ->numeric(),
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'id'),
                Forms\Components\TextInput::make('participant_user_id')
                    ->numeric(),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organizer_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participant_user_id')
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
                Tables\Columns\TextColumn::make('team.name')
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
            'index' => Pages\ListEventInvitations::route('/'),
            'create' => Pages\CreateEventInvitation::route('/create'),
            'edit' => Pages\EditEventInvitation::route('/{record}/edit'),
        ];
    }
}
