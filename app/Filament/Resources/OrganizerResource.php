<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizerResource\Pages;
use App\Filament\Resources\OrganizerResource\RelationManagers;
use App\Models\Organizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizerResource extends Resource
{
    protected static ?string $model = Organizer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('companyName')
                    ->maxLength(255),
                Forms\Components\TextInput::make('companyDescription')
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name',
                    
                    fn ($query) => $query->where('role', 'ORGANIZER')
                    
                    )
                    ->required(),
                Forms\Components\TextInput::make('stripe_customer_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('industry')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('website_link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('instagram_link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('facebook_link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('twitter_link')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('companyName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companyDescription')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_customer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('industry')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instagram_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('facebook_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_link')
                    ->searchable(),
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
            'index' => Pages\ListOrganizers::route('/'),
            'create' => Pages\CreateOrganizer::route('/create'),
            'edit' => Pages\EditOrganizer::route('/{record}/edit'),
        ];
    }
}
