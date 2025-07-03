<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizerFollowResource\Pages;
use App\Models\OrganizerFollow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;


class OrganizerFollowResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = OrganizerFollow::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('participant_user_id')
                    ->relationship('participantUser', 'name',
                    fn ($query) => $query->where('role', 'PARTICIPANT')  
                    )
                    ->required(),
                Forms\Components\Select::make('organizer_user_id')
                    ->required()
                    ->relationship('organizer', 'name',
                    fn ($query) => $query->where('role', 'ORGANIZER')             
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('participantUser.name')
                    ->numeric()
                    ->searchable()
                    ->label('Participant')
                    ->sortable(),
                Tables\Columns\TextColumn::make('organizer.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrganizerFollows::route('/'),
            // 'create' => Pages\CreateOrganizerFollow::route('/create'),
            // 'edit' => Pages\EditOrganizerFollow::route('/{record}/edit'),
        ];
    }
}
