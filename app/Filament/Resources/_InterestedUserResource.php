<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\InterestedUserResource\Pages;
// use App\Filament\Resources\InterestedUserResource\RelationManagers;
// use App\Models\InterestedUser;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

// class InterestedUserResource extends Resource
// {
//     protected static ?string $model = InterestedUser::class;


//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 //
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 //
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
//             'index' => Pages\ListInterestedUsers::route('/'),
//             // 'create' => Pages\CreateInterestedUser::route('/create'),
//             // 'edit' => Pages\EditInterestedUser::route('/{record}/edit'),
//         ];
//     }
// }
