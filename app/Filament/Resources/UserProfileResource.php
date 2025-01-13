<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProfileResource\Pages;
use App\Filament\Resources\UserProfileResource\RelationManagers;
use App\Models\UserProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('backgroundColor')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('backgroundBanner')
                    ->image(),
                Forms\Components\TextInput::make('backgroundGradient')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fontColor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('frameColor')
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('backgroundColor')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('backgroundBanner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('backgroundGradient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fontColor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('frameColor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
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
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
}
