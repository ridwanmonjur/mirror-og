<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class TeamCaptainRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'captain';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_member_id')
                    ->relationship('user', 'name', 
                        fn (Builder $query) => $query->where('role', 'PARTICIPANT')
                    )
                    ->label('Captain')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'id'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Captain Name')
                    ->sortable(),
            ])
            // ->filters([
            //     //
            // ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->visible(fn () => !$this->getOwnerRecord()->user()->exists())
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

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}