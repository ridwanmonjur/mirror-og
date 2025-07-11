<?php

namespace App\Filament\Resources\SystemCouponsResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class UserCouponRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'userCoupons';
    protected static ?string $title = 'Coupons';


    public function getDisplayNameAttribute(): string
    {
        return "Coupon #{$this->id}";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('user_id')
                // ->label('User')
                // ->relationship('user', 'name')
                // ->preload()
                // ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(function () {
                        $coupon = $this->getOwnerRecord();
                        $role = $coupon->for_type === 'organizer' ? 'ORGANIZER' : 'PARTICIPANT';
                        return User::where('role', $role)->pluck('name', 'id');
                    })
                    ->required(),
                

                Forms\Components\TextInput::make('redeemable_count')
                    ->default(1)
                    ->label('Redeem Count'),

                Forms\Components\DateTimePicker::make('redeemed_at')
                    ->label('Redeemed At')
                    ->displayFormat('Y-m-d h:i A')
                    ->required()
                    ->native(false)
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
                    ->label('User'),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('redeemable_count')
                    ->label('Redeem Count'),

                    
                
                Tables\Columns\TextColumn::make('redeemed_at')
                    ->label('Redeemed At')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Record Created')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
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

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
