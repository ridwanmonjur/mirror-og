<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchesResource\Pages;
use App\Filament\Resources\MatchesResource\RelationManagers;
use App\Models\Matches;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MatchesResource extends Resource
{
    protected static ?string $model = Matches::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('team1_id')
                    ->numeric(),
                Forms\Components\TextInput::make('team2_id')
                    ->numeric(),
                Forms\Components\TextInput::make('event_details_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('team1_position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('team2_position')
                    ->maxLength(255),
                Forms\Components\TextInput::make('stage_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('inner_stage_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team1_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team2_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_details_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team1_position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('team2_position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stage_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inner_stage_name')
                    ->searchable(),
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
            'index' => Pages\ListMatches::route('/'),
            'create' => Pages\CreateMatches::route('/create'),
            'edit' => Pages\EditMatches::route('/{record}/edit'),
        ];
    }
}
