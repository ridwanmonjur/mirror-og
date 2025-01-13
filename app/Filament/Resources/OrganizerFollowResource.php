<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizerFollowResource\Pages;
use App\Filament\Resources\OrganizerFollowResource\RelationManagers;
use App\Models\OrganizerFollow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizerFollowResource extends Resource
{
    protected static ?string $model = OrganizerFollow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('participant_user_id')
                    ->relationship('participantUser', 'name')
                    ->required(),
                Forms\Components\TextInput::make('organizer_user_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('participantUser.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organizer_user_id')
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
            'index' => Pages\ListOrganizerFollows::route('/'),
            'create' => Pages\CreateOrganizerFollow::route('/create'),
            'edit' => Pages\EditOrganizerFollow::route('/{record}/edit'),
        ];
    }
}
