<?php

namespace App\Filament\Resources\JoinEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;

class RosterRelationManager extends RelationManager
{
    protected static string $relationship = 'roster';

    public function form(Form $form): Form
    {
        return $form->schema([
            
            Forms\Components\Select::make('team_id')
            ->label('Team')
            ->options(Team::pluck('teamName', 'id'))
            ->searchable()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set) {
                // Reset user selection when team changes
                $set('user_id', null);
                $set('team_member_id', null);
            })
            ->required(),
        
        Forms\Components\Select::make('user_id')
            ->label('User')
            ->options(function (callable $get) {
                $teamId = $get('team_id');
                
                if (!$teamId) {
                    return [];
                }
                
                // Get only users who are team members of this team
                return TeamMember::where('team_id', $teamId)
                    ->join('users', 'team_members.user_id', '=', 'users.id')
                    ->pluck('users.name', 'users.id');
            })
            ->searchable()
            ->reactive()
            ->disabled(fn (callable $get) => !$get('team_id'))
            ->afterStateUpdated(function ($state, callable $set, $get) {
                if ($state && $get('team_id')) {
                    // Find the team member that connects this user to this team
                    $teamMember = TeamMember::where('user_id', $state)
                        ->where('team_id', $get('team_id'))
                        ->first();
                        
                    if ($teamMember) {
                        $set('team_member_id', $teamMember->id);
                    }
                }
            })
            ->helperText(fn (callable $get) => $get('team_id') ? 'Select a user from this team' : 'Please select a team first')
            ->required(),
        
            Forms\Components\Hidden::make('team_member_id')->required(),

            Forms\Components\Toggle::make('vote_to_quit')->label('Vote to Quit')->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([Tables\Columns\TextColumn::make('id')->sortable(), Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(), Tables\Columns\TextColumn::make('team.teamName')->label('Team')->searchable()->sortable(),  Tables\Columns\IconColumn::make('vote_to_quit')->boolean()->label('Vote to Quit')->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle'),  Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)])
            ->filters([Tables\Filters\TernaryFilter::make('vote_to_quit')->label('Vote Status'), Tables\Filters\SelectFilter::make('team_id')->relationship('team', 'teamName')->label('Team'), Tables\Filters\SelectFilter::make('user_id')->relationship('user', 'name')->label('User')])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add Join Event Roster Entry')
                    ->using(function (array $data): mixed {
                        // Make sure join_events_id is properly set from the relationship
                        $data['join_events_id'] = $this->ownerRecord->id;
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\BulkAction::make('set_vote_true')->label('Set Vote to Quit: Yes')->icon('heroicon-o-check')->action(fn($records) => $records->each->update(['vote_to_quit' => true]))->requiresConfirmation()->modalHeading('Set Vote to Quit: Yes')->modalDescription('Are you sure you want to mark these roster entries as having voted to quit?'), Tables\Actions\BulkAction::make('set_vote_false')->label('Set Vote to Quit: No')->icon('heroicon-o-x-mark')->action(fn($records) => $records->each->update(['vote_to_quit' => false]))->requiresConfirmation()->modalHeading('Set Vote to Quit: No')->modalDescription('Are you sure you want to mark these roster entries as not having voted to quit?'), Tables\Actions\DeleteBulkAction::make()])]);
    }
}
