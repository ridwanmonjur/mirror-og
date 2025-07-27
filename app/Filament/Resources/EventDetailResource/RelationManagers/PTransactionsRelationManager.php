<?php

namespace App\Filament\Resources\EventDetailResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class PTransactionsRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;
    protected static string $relationship = 'paymentTransaction';
    
    protected static ?string $recordTitleAttribute = 'payment_id';
    
    protected static ?string $title = 'Organizer Payments';

  

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Payment ID')
                    ->copyable()
                    ->copyMessage('Payment ID copied')
                    ->copyMessageDuration(1500),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email),
                
                Tables\Columns\TextColumn::make('payment_amount')
                    ->prefix('RM ')
                    ->sortable()
                    ->label('Payment Amount')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    ),
                
                Tables\Columns\TextColumn::make('discount_amount')
                    ->prefix('RM ')
                    ->sortable()
                    ->label('Discount Amount')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->default('0.00'),
                
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->prefix('RM ')
                    ->getStateUsing(fn ($record) => number_format($record->payment_amount - ($record->discount_amount ?? 0), 2))
                    ->sortable(false),
                
                Tables\Columns\TextColumn::make('history.name')
                    ->label('Transaction History')
                    
                    ->description(fn ($record) => $record->history ? "RM " . number_format($record->history->amount, 2) : null)
                    ->placeholder('No history linked'),
                
             
            ])
           
            
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ]);
            
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}