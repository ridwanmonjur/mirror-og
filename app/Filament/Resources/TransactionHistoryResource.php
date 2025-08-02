<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionHistoryResource\Pages;
use App\Models\TransactionHistory;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Traits\HandlesFilamentExceptions;

class TransactionHistoryResource extends Resource
{
    use HandlesFilamentExceptions;

    protected static ?string $model = TransactionHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

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
                    ->prefix('RM ')
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
            ->filters([
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(false) // Don't preload all users
                    ->getSearchResultsUsing(function (string $search) {
                        return User::where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->limit(15)
                            ->pluck('name', 'id');
                    })
                    ->getOptionLabelUsing(function ($value) {
                        return User::find($value)?->name;
                    })
                    ->default(function () {

                        // Set default to current authenticated user if available
                        $user = User::select(['id', 'role'])
                            ->where('role', 'PARTICIPANT')
                            ->first();

                        return $user ? $user->id : auth()->id;
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->persistFiltersInSession()
            ->filtersFormColumns(2);
    }

    public static function getEloquentQuery(): Builder
    {
        // Remove any scopes that might hide records
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListTransactionHistories::route('/'),
            // 'create' => Pages\CreateTransactionHistory::route('/create'),
            // 'edit' => Pages\EditTransactionHistory::route('/{record}/edit'),
        ];
    }
}
