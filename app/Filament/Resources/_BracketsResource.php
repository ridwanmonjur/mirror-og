<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\BracketsResource\Pages;
// use App\Filament\Resources\BracketsResource\RelationManagers;
// use App\Models\Brackets;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

// class BracketsResource extends Resource
// {
//     protected static ?string $model = Brackets::class;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\Select::make('team1_id')
//                     ->relationship('team1', 'teamName'),
//                 Forms\Components\Select::make('team2_id')
//                     ->relationship('team2', 'teamName'),
//                 Forms\Components\Select::make('event_details_id')
//                     ->relationship('event', 'eventName', function ($query) {
//                         return $query->whereNotNull('eventName');
//                     }),
//                 Forms\Components\TextInput::make('team1_position')
//                     ->maxLength(255),
//                 Forms\Components\TextInput::make('team2_position')
//                     ->maxLength(255),
//                 Forms\Components\TextInput::make('stage_name')
//                     ->maxLength(255),
//                 Forms\Components\TextInput::make('inner_stage_name')
//                     ->maxLength(255),
//                 Forms\Components\TextInput::make('order')
//                     ->required()
//                     ->numeric(),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('id'),
//                 Tables\Columns\TextColumn::make('team1.teamName')
//                     ->label('Team 1')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('team2.teamName')
//                     ->label('Team 2')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('event.eventName')
//                     ->numeric()
//                     ->searchable()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('team1_position')
//                     ->label('Pos 1')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('team2_position')
//                     ->label('Pos 2')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//                 Tables\Columns\TextColumn::make('updated_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->filters([
//                 Tables\Filters\SelectFilter::make('event_details_id')
//                     ->relationship('event', 'eventName')
//                     ->label('Event')
//                     ->searchable(),

//             ])
//             ->persistFiltersInSession()
//         ->persistSortInSession()
//         ->persistColumnSearchesInSession()
//         ->persistSearchInSession()
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListBrackets::route('/'),
//             // 'create' => Pages\CreateBrackets::route('/create'),
//             // 'edit' => Pages\EditBrackets::route('/{record}/edit'),
//         ];
//     }
// }
