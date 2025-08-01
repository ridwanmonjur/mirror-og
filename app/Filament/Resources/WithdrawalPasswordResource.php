<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalPasswordResource\Pages;
use App\Filament\Traits\HandlesFilamentExceptions;
use App\Models\WithdrawalPassword;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class WithdrawalPasswordResource extends Resource
{
    use HandlesFilamentExceptions;

    // Solution 1: Remove the static property and use a method instead
    protected static ?string $model = WithdrawalPassword::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $label = 'Withdrawal History';

    protected static ?string $pluralLabel = 'Withdrawal History';

    protected static ?string $navigationLabel = 'Withdrawal History';

    protected static ?int $navigationSort = 3;

    // Cached result to avoid multiple DB calls
    protected static ?bool $hasRecords = null;

    // Helper method to check if records exist (more efficient than count())
    protected static function hasRecords(): bool
    {
        if (static::$hasRecords === null) {
            static::$hasRecords = WithdrawalPassword::query()->exists();
        }

        return static::$hasRecords;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('password')
                ->label('Password of CSV File')
                ->required()
                ->minLength(6)
                ->maxLength(50)
                ->password()
                ->revealable()
                ->helperText('Password must be at least 6 characters long')
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('password')
                ->label('Password of CSV File Generated')
                ->formatStateUsing(fn (string $state): string => str_repeat('•', strlen($state))),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => ! static::hasRecords())
                    ->label('Create Password')
                    ->tooltip('Create the first password')
                    ->after(function () {
                        // Reset cache after creating
                        static::$hasRecords = null;
                    }),
            ])

            ->persistFiltersInSession();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return ! static::hasRecords();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            // 'create' => Pages\CreateWithdrawal::route('/create'),
            // 'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}
