<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                // Forms\Components\DateTimePicker::make('email_verified_expires_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('country_code')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('mobile_no')
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->maxLength(255),
                // Forms\Components\TextInput::make('status')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('google_id')
                    // ->maxLength(255),
                Forms\Components\FileUpload::make('userBanner')
                    ->image(),
                // Forms\Components\TextInput::make('steam_id')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('stripe_customer_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('demo_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('recovery_email')
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('userBanner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('email_verified_expires_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('country_code')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('demo_email')
                ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('recovery_email')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('google_id')
                //     ->searchable(),
                
                // Tables\Columns\TextColumn::make('steam_id')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('stripe_customer_id')
                    ->searchable(),
              
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
               
            ])
            ->filters([
                //
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
