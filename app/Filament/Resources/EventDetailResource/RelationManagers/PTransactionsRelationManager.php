<?php

namespace App\Filament\Resources\EventDetailResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentTransaction';
    
    protected static ?string $recordTitleAttribute = 'payment_id';
    
    protected static ?string $title = 'Payment Transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payment_id')
                    ->maxLength(255),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('coupon_amount')
                    ->label('Coupon Amount')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('payment_amount')
                    ->label('Payment Amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('released_amount')
                    ->label('Released Amount')
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_id')
                    ->searchable()
                    ->label('Payment Intent'),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'succeeded' => 'success',
                        'couponed' => 'primary',
                        'released' => 'primary',
                        'refunded' => 'primary',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('payment_amount')
                    ->money('USD')
                    ->sortable(),
                
                
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->visible(fn () => !$this->getOwnerRecord()->paymentTransaction()->exists())
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
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('payment_status')
                                ->label('Payment Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'completed' => 'Completed',
                                    'failed' => 'Failed',
                                    'refunded' => 'Refunded',
                                    'canceled' => 'Canceled',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'payment_status' => $data['payment_status'],
                                ]);
                            }
                        }),
                ]),
            ]);
    }
}