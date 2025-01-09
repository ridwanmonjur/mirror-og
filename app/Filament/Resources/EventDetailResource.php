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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventDefinitions')
                    ->maxLength(255),
                Forms\Components\TextInput::make('eventName')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('startDate'),
                Forms\Components\DatePicker::make('endDate'),
                Forms\Components\TextInput::make('startTime'),
                Forms\Components\TextInput::make('endTime'),
                Forms\Components\TextInput::make('eventDescription')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('eventBanner')
                    ->maxLength(255),
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
                Forms\Components\TextInput::make('event_type_id')
                    ->numeric(),
                Forms\Components\Select::make('event_tier_id')
                    ->relationship('eventTier', 'id'),
                Forms\Components\TextInput::make('event_category_id')
                    ->numeric(),
                Forms\Components\TextInput::make('payment_transaction_id')
                    ->numeric(),
                Forms\Components\Toggle::make('willNotify')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('eventBanner')
                ->searchable(),
                Tables\Columns\TextColumn::make('eventDefinitions')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eventName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('startDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('endDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('startTime'),
                Tables\Columns\TextColumn::make('endTime'),
                Tables\Columns\TextColumn::make('eventDescription')
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('eventTags')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_action_public_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_action_public_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_action_private')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
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
                Tables\Columns\TextColumn::make('event_type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eventTier.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_transaction_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('willNotify')
                    ->boolean(),
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
            'index' => Pages\ListEventDetails::route('/'),
            'create' => Pages\CreateEventDetail::route('/create'),
            'edit' => Pages\EditEventDetail::route('/{record}/edit'),
        ];
    }
}
