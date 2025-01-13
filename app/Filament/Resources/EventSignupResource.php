<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventSignupResource\Pages;
use App\Filament\Resources\EventSignupResource\RelationManagers;
use App\Models\EventSignup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventSignupResource extends Resource
{
    protected static ?string $model = EventSignup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_id')
                ->relationship(
                    'eventDetails',
                    'eventName',
                    fn ($query) => $query->whereNotNull('eventName')
                ),
                Forms\Components\DateTimePicker::make('signup_open')
                    ->required(),
                Forms\Components\DateTimePicker::make('normal_signup_start_advanced_close')
                    ->required(),
                Forms\Components\DateTimePicker::make('signup_close')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eventDetails.eventName')
                    ->label('Event')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_open')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('normal_signup_start_advanced_close')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_close')
                    ->dateTime()
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
            'index' => Pages\ListEventSignups::route('/'),
            'create' => Pages\CreateEventSignup::route('/create'),
            'edit' => Pages\EditEventSignup::route('/{record}/edit'),
        ];
    }
}
