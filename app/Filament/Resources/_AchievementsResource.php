<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\AchievementsResource\Pages;
// use App\Filament\Resources\AchievementsResource\RelationManagers;
// use App\Models\Achievements;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

// class AchievementsResource extends Resource
// {
//     protected static ?string $model = Achievements::class;


//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\TextInput::make('title')
//                     ->required()
//                     ->maxLength(255),
//                 Forms\Components\Textarea::make('description')
//                     ->required()
//                     ->columnSpanFull(),
//                 Forms\Components\TextInput::make('join_event_id')
//                     ->required()
//                     ->numeric(),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('id'),

//                 Tables\Columns\TextColumn::make('title')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('join_event_id')
//                     ->numeric()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->filters([
//                 //
//             ])
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
//             'index' => Pages\ListAchievements::route('/'),
//             // 'create' => Pages\CreateAchievements::route('/create'),
//             // 'edit' => Pages\EditAchievements::route('/{record}/edit'),
//         ];
//     }
// }
