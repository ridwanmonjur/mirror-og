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
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Transaction name'),

                                Forms\Components\TextInput::make('type')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Write transaction type'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->prefix('RM')
                                    ->placeholder('0.00'),

                                Forms\Components\Toggle::make('isPositive')
                                    ->label('Positive Amount')
                                    ->helperText('Toggle on for income/credits, off for expenses/debits')
                                    ->default(true),
                            ]),

                        Forms\Components\DateTimePicker::make('date')
                            ->required()
                            ->default(now())
                            ->displayFormat('M j, Y g:i A')
                            ->timezone('Asia/Kuala_Lumpur')
                            ->seconds(false)
                            ->native(false)
                            ->placeholder('Select date and time'),

                        Forms\Components\TextInput::make('link')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com')
                            ->helperText('Optional link related to this transaction'),

                        Forms\Components\Textarea::make('summary')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Additional details about this transaction...'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->tooltip(fn (TransactionHistory $record): string => $record->summary ?? 'No summary'),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                        'warning' => 'transfer',
                        'info' => 'investment',
                        'gray' => ['fee', 'other'],
                        'primary' => 'refund',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->color(fn (TransactionHistory $record): string => $record->isPositive ? 'success' : 'danger')
                    ->weight('bold')
                    ->sortable(['amount'])
                    ->alignEnd(),

                Tables\Columns\IconColumn::make('isPositive')
                    ->label('Type')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-trending-up')
                    ->falseIcon('heroicon-o-arrow-trending-down')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (TransactionHistory $record): string => $record->isPositive ? 'Credit' : 'Debit'),

                Tables\Columns\TextColumn::make('date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('link')
                    ->label('Link')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'View' : '')
                    ->url(fn (TransactionHistory $record): ?string => $record->link)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
           
            ;
            
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
