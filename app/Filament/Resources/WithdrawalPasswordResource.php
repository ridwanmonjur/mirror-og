<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalPasswordResource\Pages;
use App\Filament\Traits\HandlesFilamentExceptions;
use App\Models\WithdrawalPassword;
use App\Models\User;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class WithdrawalPasswordResource extends Resource
{
    use HandlesFilamentExceptions;
    
    protected static ?string $model = WithdrawalPassword::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationLabel = 'Withdrawal History';

    protected static ?int $navigationSort = 3;

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
                ->formatStateUsing(fn (string $state): string => str_repeat('•', strlen($state)))
                ->searchable()
                ->sortable(),
            ])
            
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\CreateAction::make(),
            ])
            
            ->persistFiltersInSession()
            ->filtersFormColumns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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