<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventCategoryResource\Pages;
use App\Filament\Resources\EventCategoryResource\RelationManagers;
use App\Models\EventCategory;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventCategoryResource extends Resource
{
    protected static ?string $model = EventCategory::class;
    public static ?string $navigationGroup = 'Manage Event';
    protected static ?string $navigationIcon = 'heroicon-o-tag';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('gameTitle')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('gameIcon')
                    ->preserveFilenames()
                    ->required()->columnSpan($span = 1),
                Forms\Components\TextInput::make('eventType')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('eventDefinitions')
                    ->required()
                    ->maxLength(955)->columnSpan($span = 1),
                Forms\Components\TextInput::make('eventTier')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\FileUpload::make('tierIcon')
                    ->preserveFilenames()
                    ->required()->columnSpan($span = 1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                Tables\Columns\TextColumn::make('gameTitle')->searchable()->sortable(),
                Tables\Columns\ImageColumn::make('gameIcon'),
                Tables\Columns\TextColumn::make('eventType'),
                Tables\Columns\TextColumn::make('eventDefinitions')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('eventTier'),
                Tables\Columns\ImageColumn::make('tierIcon'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEventCategories::route('/'),
            'create' => Pages\CreateEventCategory::route('/create'),
            'edit' => Pages\EditEventCategory::route('/{record}/edit'),
        ];
    }
}
