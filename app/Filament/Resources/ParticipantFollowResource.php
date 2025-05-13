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

class ParticipantFollowResource extends Resource
{
    protected static ?string $model = ParticipantFollow::class;


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
                    ->relationship('followerUser', 'name'),

                Forms\Components\Select::make('participant_followee')
                    ->label('Followee')
                    ->options(User::where('role', 'PARTICIPANT')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('follower_role')
                    ->label('Follower Role')
                    ->options([
                        'PARTICIPANT' => 'Participant',
                        'ORGANIZER' => 'Organizer',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, $role): Builder => $query->whereHas('followerUser', fn ($q) => $q->where('role', $role))
                            );
                    }),
                
                Tables\Filters\SelectFilter::make('followee_role')
                    ->label('Followee Role')
                    ->options([
                        'PARTICIPANT' => 'Participant',
                        'ORGANIZER' => 'Organizer',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, $role): Builder => $query->whereHas('followeeUser', fn ($q) => $q->where('role', $role))
                            );
                    }),
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