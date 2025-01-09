<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventJoinResultsResource\Pages;
use App\Filament\Resources\EventJoinResultsResource\RelationManagers;
use App\Models\EventJoinResults;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventJoinResultsResource extends Resource
{
    protected static ?string $model = EventJoinResults::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('join_events_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('position')
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
                Tables\Columns\TextColumn::make('position')
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
            'index' => Pages\ListEventJoinResults::route('/'),
            'create' => Pages\CreateEventJoinResults::route('/create'),
            'edit' => Pages\EditEventJoinResults::route('/{record}/edit'),
        ];
    }
}
