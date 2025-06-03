<?php

namespace App\Filament\Resources\EventDetailResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SignupRelationManager extends RelationManager
{
    protected static string $relationship = 'signup';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('signup_open')
                    ->required(),
                Forms\Components\DateTimePicker::make('normal_signup_start_advanced_close')
                    ->required(),
                Forms\Components\DateTimePicker::make('signup_close')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('signup_open')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('normal_signup_start_advanced_close')
                    ->label('Normal Start/Advanced Close')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_close')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
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