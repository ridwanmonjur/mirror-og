<?php

namespace App\Filament\Resources\EventTierResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TierSignupRelationManager extends RelationManager
{
    protected static string $relationship = 'tierSignups';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'eventType')
                    ->required(),
                Forms\Components\TextInput::make('signup_open')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('signup_close')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('normal_signup_start_advanced_close')
                    ->label('Normal signup start/Advanced close')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('type.eventType')
                    ->label('Event Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_open')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('normal_signup_start_advanced_close')
                    ->label('Normal-Advanced time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_close')
                    ->numeric()
                    ->sortable(),
            ])
            // ->filters([
            //     //
            // ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}