<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class AddressRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'address';

    protected static bool $hasAssociatedRecord = true;

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
                Forms\Components\TextInput::make('country')
                    ->required(),
                // user_id will be automatically handled by Filament
                // since this is a relation manager
            ]);
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('city')
            ->columns([
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
                    ->visible(fn () => ! $this->getOwnerRecord()->address()->exists())
                    // ->successRedirectUrl(fn () => $this->getParentResource()::getUrl('index'))
                    ->createAnother(false),
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
