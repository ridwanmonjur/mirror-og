<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantFollowResource\Pages;
use App\Models\ParticipantFollow;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HandlesFilamentExceptions;

class ParticipantFollowResource extends Resource
{
    protected static ?string $model = ParticipantFollow::class;

    use HandlesFilamentExceptions;

    protected static ?string $navigationLabel = 'Participant Follows';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('participant_follower')
                    ->label('Follower')
                    ->options(User::where('role', 'PARTICIPANT')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->rules([
                        fn ($get) => function ($attribute, $value, $fail) use ($get) {
                            if ($value && $value == $get('participant_followee')) {
                                $fail('A user cannot follow themselves. Please select different users for Follower and Followee.');
                            }
                        },
                    ])
                    ->relationship('followerUser', 'name'),

                Forms\Components\Select::make('participant_followee')
                    ->label('Followee')
                    ->options(User::where('role', 'PARTICIPANT')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->rules([
                        fn ($get) => function ($attribute, $value, $fail) use ($get) {
                            if ($value && $value == $get('participant_follower')) {
                                $fail('A user cannot follow themselves. Please select different users for Follower and Followee.');
                            }
                        },
                    ])
                    ->relationship('followeeUser', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('followerUser.name')
                    ->label('Follower Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('followeeUser.name')
                    ->label('Followee Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

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
            'index' => Pages\ListParticipantFollows::route('/'),
            // 'create' => Pages\CreateParticipantFollow::route('/create'),
            // 'edit' => Pages\EditParticipantFollow::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['followerUser', 'followeeUser']);
    }
}
