<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JoinEventResource\Pages;
use App\Filament\Resources\JoinEventResource\RelationManagers;
use App\Models\JoinEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JoinEventResource extends Resource
{
    protected static ?string $model = JoinEvent::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_details_id')
                    ->relationship('eventDetails', 'id')
                    ->required(),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'id')
                    ->required(),
                Forms\Components\Select::make('joiner_id')
                    ->required()
                    ->label('Participant')
                    ->options(function () {
                        return \App\Models\User::where('role', 'PARTICIPANT')
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            // Find the participant ID related to the selected user
                            $participant = \App\Models\User::find($state)?->participant;
                            if ($participant) {
                                $set('joiner_participant_id', $participant->id);
                            }
                        }
                    }),
                
                // Hidden field for joiner_participant_id
                Forms\Components\Hidden::make('joiner_participant_id')
                    ->required(),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\TextInput::make('join_status')
                    ->required(),
                Forms\Components\Select::make('vote_starter_id')
                    ->relationship('voteStarter', 'name')
                    ->required(),
                Forms\Components\Toggle::make('vote_ongoing'),
                Forms\Components\Select::make('roster_captain_id')
                    ->required()
                    ->label('Roster Captain')
                    ->options(function (callable $get) {
                        // Get the selected team ID
                        $teamId = $get('team_id');
                        
                        if (!$teamId) {
                            return [];
                        }
                        
                        // Get roster members belonging to the selected team
                        return \App\Models\RosterMember::query()
                            ->where('team_id', $teamId)
                            ->with('user')
                            ->get()
                            ->mapWithKeys(function ($member) {
                                return [$member->id => $member->user->name];
                            });
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('eventDetails.eventName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('team.teamName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status'),
                Tables\Columns\TextColumn::make('join_status'),
                Tables\Columns\TextColumn::make('voteStarter.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('vote_ongoing')
                    ->boolean(),
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
            'index' => Pages\ListJoinEvents::route('/'),
            'create' => Pages\CreateJoinEvent::route('/create'),
            'edit' => Pages\EditJoinEvent::route('/{record}/edit'),
        ];
    }
}
