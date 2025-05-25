<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionHistoryResource\Pages;
use App\Filament\Resources\TransactionHistoryResource\RelationManagers;
use App\Models\TransactionHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionHistoryResource extends Resource
{
    protected static ?string $model = TransactionHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTransactionHistories::route('/'),
            // 'create' => Pages\CreateTransactionHistory::route('/create'),
            // 'edit' => Pages\EditTransactionHistory::route('/{record}/edit'),
        ];
    }
}
