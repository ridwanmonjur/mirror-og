<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlocksResource\Pages;
use App\Filament\Resources\BlocksResource\RelationManagers;
use App\Models\Blocks;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlocksResource extends Resource
{
    protected static ?string $model = Blocks::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->required()
                    ->relationship('user', 'name'),
                Forms\Components\Select::make('blocked_user_id')
                    ->required()
                    ->different('user_id')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->label('Blocked User')
                    ->relationship('blockedUser', 'name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user_name')
                    ->label('User')
                    ->getStateUsing(fn ($record) => $record->user->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('blocked_user_name')
                    ->label('Blocked User')
                    ->getStateUsing(fn ($record) => $record->blockedUser->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    
                    ->timezone('Asia/Kuala_Lumpur')
                    ->dateTime('M d, Y — h:i A') 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    
                    ->timezone('Asia/Kuala_Lumpur')
                    ->dateTime('M d, Y — h:i A') 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBlocks::route('/'),
            // 'create' => Pages\CreateBlocks::route('/create'),
            // 'edit' => Pages\EditBlocks::route('/{record}/edit'),
        ];
    }
}