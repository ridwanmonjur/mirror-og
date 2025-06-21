<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantCouponsResource\Pages;
use App\Filament\Resources\ParticipantCouponsResource\RelationManagers;
use App\Models\ParticipantCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParticipantCouponsResource extends Resource
{
    protected static ?string $model = ParticipantCoupon::class;

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
                        ->prefix('RM')
                        ->minValue(0)
                        ->maxValue(9999999.99)
                        ->columnSpan(1),
                    
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Expires At')
                        ->displayFormat('Y-m-d h:i A')
                        ->timezone('Asia/Kuala_Lumpur')
                        ->seconds(false)
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
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                
                Tables\Columns\TextColumn::make('amount')
                    ->prefix('RM ')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean()
                    ->label('Public'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->placeholder('Never'),
           
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
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
            'index' => Pages\ListParticipantCoupons::route('/'),
            // 'create' => Pages\CreateParticipantCoupons::route('/create'),
            'edit' => Pages\EditParticipantCoupons::route('/{record}/edit'),
        ];
    }
}
