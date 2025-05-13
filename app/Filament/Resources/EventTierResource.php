<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventTierResource\Pages;
use App\Filament\Resources\EventTierResource\RelationManagers;
use App\Models\EventTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventTierResource extends Resource
{
    protected static ?string $model = EventTier::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventTier')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('tierIcon')
                    ->image(),
                Forms\Components\TextInput::make('tierTeamSlot')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tierPrizePool')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tierEntryFee')
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('eventTier')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('tierIcon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tierTeamSlot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tierPrizePool')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tierEntryFee')
                    ->searchable(),
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
            RelationManagers\TierSignupRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventTiers::route('/'),
            // 'create' => Pages\CreateEventTier::route('/create'),
            'edit' => Pages\EditEventTier::route('/{record}/edit'),
        ];
    }
}
