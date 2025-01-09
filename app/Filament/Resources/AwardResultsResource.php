<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AwardResultsResource\Pages;
use App\Filament\Resources\AwardResultsResource\RelationManagers;
use App\Models\AwardResults;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AwardResultsResource extends Resource
{
    protected static ?string $model = AwardResults::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('join_events_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('award_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('team_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('join_events_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('award_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_id')
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
            'index' => Pages\ListAwardResults::route('/'),
            'create' => Pages\CreateAwardResults::route('/create'),
            'edit' => Pages\EditAwardResults::route('/{record}/edit'),
        ];
    }
}
