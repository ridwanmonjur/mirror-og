<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventCreateCouponResource\Pages;
use App\Models\EventCreateCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventCreateCouponResource extends Resource
{
    protected static ?string $model = EventCreateCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'Organizer Coupons';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter coupon name'),
                            
                        Forms\Components\TextInput::make('coupon')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter coupon code'),
                            
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'percent' => 'Percentage',
                                'sum' => 'Fixed Amount',
                            ])
                            ->default('percentage'),
                            
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->rules([
                                function (Forms\Get $get) {
                                    return $get('type') === 'percent'
                                        ? 'max:100'
                                        : null;
                                }
                            ])
                            ->placeholder('Enter discount amount'),
                    ])->columns(2),
                            
                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DatePicker::make('startDate')
                            ->required()
                            ->label('Start Date'),
                            
                        Forms\Components\DatePicker::make('endDate')
                            ->required()
                            ->label('End Date')
                            ->afterOrEqual('startDate'),
                            
                        Forms\Components\TimePicker::make('startTime')
                            ->required()
                            ->seconds(false)
                            ->label('Start Time'),
                            
                        Forms\Components\TimePicker::make('endTime')
                            ->required()
                            ->seconds(false)
                            ->label('End Time'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('isEnforced')
                            ->label('Enforce Coupon')
                            ->helperText('If enabled, this coupon will be enforced')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('coupon')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percent' => 'success',
                        'sum' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, EventCreateCoupon $record): string => 
                        $record->type === 'percent' ? "{$state}%" : "RM {$state}"),
                    
                Tables\Columns\TextColumn::make('startDate')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('endDate')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('isEnforced')
                    ->boolean()
                    ->label('Enforced'),
                    
             
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percent' => 'Percentage',
                        'sum' => 'Fixed Amount',
                    ]),
                    
                Tables\Filters\Filter::make('active')
                    ->label('Active Coupons')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereDate('startDate', '<=', now())
                        ->whereDate('endDate', '>=', now())),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventCreateCoupons::route('/'),
            // 'create' => Pages\CreateOrganizerCoupon::route('/create'),
            // 'edit' => Pages\EditOrganizerCoupon::route('/{record}/edit'),
        ];
    }
}