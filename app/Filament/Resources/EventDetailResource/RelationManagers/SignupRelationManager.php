<?php

namespace App\Filament\Resources\EventDetailResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SignupRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;
    protected static string $relationship = 'signup';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('signup_open')
                    ->seconds(false)   
                    ->native(false) 
                    ->displayFormat('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->required(),
                Forms\Components\DateTimePicker::make('normal_signup_start_advanced_close')
                    ->seconds(false)
                    ->native(false) 
                    ->displayFormat('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->required(),
                Forms\Components\DateTimePicker::make('signup_close')
                    ->seconds(false)
                    ->native(false) 
                    ->displayFormat('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('signup_open')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('normal_signup_start_advanced_close')
                    ->label('Normal Start/Advanced Close')
                    ->dateTime('Y-m-d h:i A')
                    
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('signup_close')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
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