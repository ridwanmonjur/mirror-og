<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    protected static bool $hasAssociatedRecord = true;
    protected static ?string $recordTitleAttribute = 'city';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('addressLine1')
                    ->required()
                    ->maxLength(255)
                    ->label('Address Line 1'),
                Forms\Components\TextInput::make('addressLine2')
                    ->maxLength(255)
                    ->label('Address Line 2'),
                Forms\Components\TextInput::make('postcode')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('country')
                    ->required()
                // user_id will be automatically handled by Filament
                // since this is a relation manager
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('city')
            ->columns([
                Tables\Columns\TextColumn::make('postcode')
                ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
              
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
            ])
            // ->filters([
            //     //
            // ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => !$this->getOwnerRecord()->address()->exists())
                    // ->successRedirectUrl(fn () => $this->getParentResource()::getUrl('index'))
                    ->createAnother(false)
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