<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventDetailResource\Pages;
use App\Filament\Resources\EventDetailResource\RelationManagers;
use App\Models\EventDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventDetailResource extends Resource
{
    protected static ?string $model = EventDetail::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventName')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('startDate'),
                Forms\Components\DatePicker::make('endDate'),
                Forms\Components\TextInput::make('startTime'),
                Forms\Components\TextInput::make('endTime'),
                Forms\Components\Textarea::make('eventDescription'),
                Forms\Components\FileUpload::make('eventBanner')
                    ->image(),
                Forms\Components\TextInput::make('eventTags')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('venue')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sub_action_public_date')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sub_action_public_time')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sub_action_private')
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('event_type_id')
                    ->relationship('type', 'eventType'),
                Forms\Components\Select::make('event_tier_id')
                    ->relationship('eventTier', 'eventTier'),
                Forms\Components\Select::make('event_category_id')
                    ->relationship('game', 'gameTitle'),
                Forms\Components\Toggle::make('willNotify')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('eventBanner')
                ->searchable(),
                Tables\Columns\TextColumn::make('eventName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.eventType')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eventTier.eventTier')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.gameTitle')
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
            RelationManagers\SignupRelationManager::class,
            RelationManagers\PTransactionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventDetails::route('/'),
            'create' => Pages\CreateEventDetail::route('/create'),
            'edit' => Pages\EditEventDetail::route('/{record}/edit'),
        ];
    }
}
