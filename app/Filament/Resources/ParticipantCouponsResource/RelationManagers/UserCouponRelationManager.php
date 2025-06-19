<?php

namespace App\Filament\Resources\ParticipantCouponsResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserCouponRelationManager extends RelationManager
{
    protected static string $relationship = 'userCoupons';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('user_id')
                // ->label('User')
                // ->relationship('user', 'name')
                // ->searchable()
                // ->preload()
                // ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(User::where('role', 'PARTICIPANT')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->relationship('user', 'name'),
                
                Forms\Components\DateTimePicker::make('redeemed_at')
                    ->label('Redeemed At')
                    ->displayFormat('Y-m-d h:i A')
                    ->required()
                    ->seconds(false)
                    ->timezone('Asia/Kuala_Lumpur')
                    ->default(now()),
                ]
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('redeemed_at')
                    ->label('Redeemed At')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Record Created')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            // ->filters([
            //     //
            // ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
