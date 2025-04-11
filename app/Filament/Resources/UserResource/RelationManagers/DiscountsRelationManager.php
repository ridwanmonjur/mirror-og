<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountsRelationManager extends RelationManager
{
    protected static string $relationship = 'discounts';

    protected static ?string $recordTitleAttribute = 'amount';

     

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('RM')                   
                    ->label('Discount Amount'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->prefix('RM ')     
                    ->sortable()
                    ->searchable(),
              
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = $this->getOwnerRecord()->id;
                    return $data;
                }),
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
}