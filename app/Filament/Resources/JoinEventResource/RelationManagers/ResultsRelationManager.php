<?php

namespace App\Filament\Resources\JoinEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ResultsRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'results';

    protected static ?string $recordTitleAttribute = 'position';

    protected static ?string $title = 'Results';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // No need to show join_events_id as it will be automatically handled
                // by the relation manager
                Forms\Components\TextInput::make('position')
                    ->label('Position')
                    ->integer()
                    ->minValue(1)
                    ->required(),
                Forms\Components\TextInput::make('prize_sum')
                    ->label('Prize Sum')
                    ->numeric()
                    ->prefix('RM ')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('position')
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_sum')
                    ->prefix('RM ')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => ! $this->getOwnerRecord()->results()->exists())
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
