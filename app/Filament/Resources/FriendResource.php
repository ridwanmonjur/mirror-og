<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FriendResource\Pages;
use App\Models\Friend;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Traits\HandlesFilamentExceptions;

class FriendResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = Friend::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user1_id')
                    ->relationship('user1', 'name', 
                    fn ($query) => $query->where('role', 'PARTICIPANT') 
                    )
                    ->label('User 1')
                    ->required(),
                Forms\Components\Select::make('user2_id')
                    ->label('User 2')    
                    ->required()
                    ->different('user1_id')
                    ->relationship('user2', 'name', 
                        fn ($query) => $query->where('role', 'PARTICIPANT') 
                ),
               
                Forms\Components\Select::make('actor_id')
                    ->required()
                    ->rules([
                        fn ($get) => function ($attribute, $value, $fail) use ($get) {
                            $user1 = $get('user1_id');
                            $user2 = $get('user2_id');
                            if ($value != $user1 && $value != $user2) {
                                $fail('Actor must be either User 1 or User 2.');
                            }
                        }
                    ])
                    ->relationship('actor', 'name'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['pending', 'accepted','rejected','left']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('user1.name')
                    ->label('User 1')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user2.name')
                    ->label('User 2')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Actor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status'),
                
            ])
            // ->filters([
            //     //
            // ])
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
            'index' => Pages\ListFriends::route('/'),
            // 'create' => Pages\CreateFriend::route('/create'),
            // 'edit' => Pages\EditFriend::route('/{record}/edit'),
        ];
    }
}
