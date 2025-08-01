<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class DiscountsRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'wallet';

    protected static bool $hasAssociatedRecord = true;

    protected static ?string $recordTitleAttribute = 'amount';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('usable_balance')
                        ->required()
                        ->numeric()
                        ->prefix('RM ')
                        ->label('Usable Balance'),
                    Forms\Components\TextInput::make('current_balance')
                        ->required()
                        ->numeric()
                        ->prefix('RM ')
                        ->label('Current Balance'),
                ])
                ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usable_balance')
                    ->prefix('RM ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->prefix('RM ')
                    ->sortable(),

            ])

            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->visible(fn () => ! $this->getOwnerRecord()->wallet()->exists())
                    // ->successRedirectUrl(fn () => $this->getParentResource()::getUrl('index'))
                    ->createAnother(false)

                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = $this->getOwnerRecord()->id;

                    return $data;
                }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
