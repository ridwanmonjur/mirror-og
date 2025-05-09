<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventTierSignupResource\Pages;
use App\Filament\Resources\EventTierSignupResource\RelationManagers;
use App\Models\EventTierSignup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventTierSignupResource extends Resource
{
    protected static ?string $model = EventTierSignup::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tier_id')
                    ->relationship('tier', 'id')
                    ->required(),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'id')
                    ->required(),
                Forms\Components\TextInput::make('signup_open')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('signup_close')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('normal_signup_start_advanced_close')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tier.eventTier')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.eventType')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_open')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_close')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('normal_signup_start_advanced_close')
                    ->label('Normal-Advanced time')
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
            'index' => Pages\ListEventTierSignups::route('/'),
            'create' => Pages\CreateEventTierSignup::route('/create'),
            'edit' => Pages\EditEventTierSignup::route('/{record}/edit'),
        ];
    }
}
