<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JoinEventResource\Pages;
use App\Models\JoinEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;

class JoinEventResource extends Resource
{
    use HandlesFilamentExceptions;

    protected static ?string $model = JoinEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Select::make('event_details_id')
                    ->relationship('eventDetails', 'eventName', function ($query) {
                        return $query->whereNotNull('eventName');
                    })
                    ->required(),
                Forms\Components\TextInput::make('results.position')
                    ->maxLength(25),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'teamName')
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
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'waived' => 'Waived',
                    ])
                    ->required()
                    ->default('pending'),

                Forms\Components\Select::make('join_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Select::make('vote_starter_id')
                    ->options(function (callable $get) {
                        // Get the selected team ID
                        $id = $get('id');

                        if (! $id) {
                            return [];
                        }

                        // Get roster members belonging to the selected team
                        return \App\Models\RosterMember::query()
                            ->where('join_events_id', $id)
                            ->with('user')
                            ->get()
                            ->mapWithKeys(function ($member) {
                                return [$member->id => $member->user->name];
                            });
                    })
                    ->required(),
                Forms\Components\Select::make('roster_captain_id')
                    ->label('Roster Captain')
                    ->options(function (callable $get) {
                        // Get the selected team ID
                        $id = $get('id');

                        if (! $id) {
                            return [];
                        }

                        // Get roster members belonging to the selected team
                        return \App\Models\RosterMember::query()
                            ->where('join_events_id', $id)
                            ->with('user')
                            ->get()
                            ->mapWithKeys(function ($member) {
                                return [$member->id => $member->user->name];
                            });
                    }),
                Forms\Components\Toggle::make('vote_ongoing'),

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
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
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

            ])
            // ->filters([
            //     //
            // ])
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
            JoinEventResource\RelationManagers\RosterRelationManager::class,
            JoinEventResource\RelationManagers\ResultsRelationManager::class,
            JoinEventResource\RelationManagers\PaymentsRelationManager::class,
            // JoinEventResource\RelationManagers\RosterHistoryRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJoinEvents::route('/'),
            // 'create' => Pages\CreateJoinEvent::route('/create'),
            'edit' => Pages\EditJoinEvent::route('/{record}/edit'),
        ];
    }
}
