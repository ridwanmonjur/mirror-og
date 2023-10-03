<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventDetailResource\Pages;
use App\Filament\Resources\EventDetailResource\RelationManagers;
use App\Models\EventDetail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventDetailResource extends Resource
{
    protected static ?string $model = EventDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    public static ?string $navigationGroup = 'Manage Event';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('eventName')
                    ->required()
                    ->maxLength(255)->columnSpan($span = 4),
                Forms\Components\Textarea::make('eventDescription')
                    ->required()
                    ->maxLength(955)->columnSpan($span = 4),
                Forms\Components\DatePicker::make('startDate')
                    ->required(),
                Forms\Components\DatePicker::make('endDate')
                    ->required(),
                Forms\Components\TimePicker::make('startTime')
                    ->required(),
                Forms\Components\TimePicker::make('endTime')
                    ->required(),
                Forms\Components\FileUpload::make('eventBanner')
                    ->preserveFilenames()
                    ->required()->columnSpan($span = 4),
                Forms\Components\TagsInput::make('eventTags')
                    ->required()->columnSpan($span = 4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

        ->columns([
            Tables\Columns\TextColumn::make('eventName')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('startDate')
                ->date(),
            Tables\Columns\TextColumn::make('endDate')
                ->date(),
            Tables\Columns\TextColumn::make('startTime'),
            Tables\Columns\TextColumn::make('endTime'),
            Tables\Columns\TextColumn::make('eventDescription')->limit(30)->toggleable(),
            Tables\Columns\ImageColumn::make('eventBanner'),
            Tables\Columns\TextColumn::make('eventTags'),
            Tables\Columns\TextColumn::make('created_at')
            ->toggleable()->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')
            ->toggleable()->dateTime(),
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
            'index' => Pages\ListEventDetails::route('/'),
            'create' => Pages\CreateEventDetail::route('/create'),
            'edit' => Pages\EditEventDetail::route('/{record}/edit'),
        ];
    }
}
