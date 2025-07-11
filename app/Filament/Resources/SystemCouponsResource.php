<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemCouponsResource\Pages;
use App\Filament\Resources\SystemCouponsResource\RelationManagers;
use App\Models\SystemCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;

class SystemCouponsResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = SystemCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpan(1),
                    
                    Forms\Components\TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('RM ')
                        ->minValue(0)
                        ->maxValue(9999999.99)
                        ->columnSpan(1),
                    
                    Forms\Components\Select::make('for_type')
                        ->label('Role')
                        ->options([
                            'organizer' => 'Organizer',
                            'participant' => 'Participant',
                        ])
                        ->default('participant')
                        ->disabled(fn ($record) => $record !== null)
                        ->columnSpan(1),
                    
                    Forms\Components\TextInput::make('redeem_count')
                        ->label('Redeem Count')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(9999)
                        ->default(1)
                        ->columnSpan(1),
                    
                    Forms\Components\Select::make('discount_type')
                        ->label('Discount Type')
                        ->options([
                            'sum' => 'Sum',
                            'percentage' => 'Percentage',
                        ])
                        ->default('sum')
                        ->columnSpan(1),
                    
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Expires At')
                        ->displayFormat('Y-m-d h:i A')
                        ->timezone('Asia/Kuala_Lumpur')
                        ->seconds(false)
                        ->native(false)
                        ->nullable()
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('Settings')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpan(1),
                    
                    Forms\Components\Toggle::make('is_public')
                        ->label('Public')
                        ->default(true)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Forms\Components\Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->copyable(),
                
                
                Tables\Columns\TextColumn::make('amount')
                    ->prefix('RM '),
                
                Tables\Columns\TextColumn::make('for_type')
                    ->label('Role Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'organizer' => 'success',
                        'participant' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('redeem_count')
                    ->label('Redeem Count')
                    ->numeric(),
                
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Discount Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sum' => 'success',
                        'percentage' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean()
                    ->label('Public'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    
                    ->placeholder('Never'),
           
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
                //
            // ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserCouponRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemCoupons::route('/'),
            // 'create' => Pages\CreateSystemCoupons::route('/create'),
            'edit' => Pages\EditSystemCoupons::route('/{record}/edit'),
        ];
    }
}
