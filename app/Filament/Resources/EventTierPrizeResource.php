<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventTierPrizeResource\Pages;
use App\Filament\Resources\EventTierPrizeResource\RelationManagers;
use App\Models\EventTierPrize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventTierPrizeResource extends Resource
{
    protected static ?string $model = EventTierPrize::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_tier_id')
                    ->relationship('eventTier', 'eventTier')
                    ->required(),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->maxValue(999),
                Forms\Components\TextInput::make('prize_sum')
                    ->required()
                    ->prefix('RM '),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eventTier.eventTier')
                    ->label('Event Tier')
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_sum')
                    ->prefix('RM ')
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
            'index' => Pages\ListEventTierPrizes::route('/'),
            // 'create' => Pages\CreateEventTierPrize::route('/create'),
            // 'edit' => Pages\EditEventTierPrize::route('/{record}/edit'),
        ];
    }
}
