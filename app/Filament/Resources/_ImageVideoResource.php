<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\ImageVideoResource\Pages;
// use App\Filament\Resources\ImageVideoResource\RelationManagers;
// use App\Models\ImageVideo;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

// class ImageVideoResource extends Resource
// {
//     protected static ?string $model = ImageVideo::class;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\FileUpload::make('file_path')
//                     ->image()
//                     ->video(),
//                 Forms\Components\TextInput::make('file_type')
//                     ->required(),
//                 Forms\Components\TextInput::make('mime_type')
//                     ->required()
//                     ->maxLength(255),
//                 Forms\Components\TextInput::make('size')
//                     ->required()
//                     ->numeric(),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\ImageColumn::make('file_path'),
//                 Tables\Columns\TextColumn::make('file_type'),
//                 Tables\Columns\TextColumn::make('mime_type')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('size')
//                     ->numeric()
//                     ->sortable(),
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
//             'index' => Pages\ListImageVideos::route('/'),
//             // 'create' => Pages\CreateImageVideo::route('/create'),
//             // 'edit' => Pages\EditImageVideo::route('/{record}/edit'),
//         ];
//     }
// }
