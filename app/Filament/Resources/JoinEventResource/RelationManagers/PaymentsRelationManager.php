<?php

namespace App\Filament\Resources\JoinEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;

class PaymentsRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'payments';
    
    protected static ?string $recordTitleAttribute = 'payment_id';
    
    protected static ?string $title = 'Payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_members_id')
                ->label('Team Member')
                ->options(function (RelationManager $livewire): array {
                    // Get the parent record (JoinEvent)
                    $joinEvent = $livewire->ownerRecord;
                    
                    // Get the team_id from the parent JoinEvent
                    $teamId = $joinEvent->team_id;
                    
                    if (!$teamId) {
                        return [];
                    }
                    
                    // Get all team members for this team with their user names
                    return \App\Models\TeamMember::query()
                        ->where('team_id', $teamId)
                        ->with('user')
                        ->get()
                        ->mapWithKeys(function ($member) {
                            // Only include members who have a related user with a name
                            if ($member->user && $member->user->name) {
                                return [$member->id => $member->user->name];
                            }
                            return [];
                        })
                        ->toArray();
                })
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        // Find the user_id related to the selected team member
                        $teamMember = \App\Models\TeamMember::find($state);
                        if ($teamMember && $teamMember->user_id) {
                            $set('user_id', $teamMember->user_id);
                        }
                    }
                }),
                
            // Hidden field for user_id, will be set automatically based on team_members_id
            Forms\Components\Hidden::make('user_id')
                ->required(),
                    
            Forms\Components\Select::make('payment_id')
                ->label('Payment Transaction')
                ->options(function () {
                    $transactions = \App\Models\RecordStripe::all();
                    
                    if ($transactions->isEmpty()) {
                        return ['' => 'N/A - No transactions available'];
                    }
                    
                    return $transactions->mapWithKeys(function ($transaction) {
                        $displayValue = "{$transaction->id} | $" . number_format($transaction->payment_amount, 2) . " | {$transaction->payment_status}";
                        return [$transaction->id => $displayValue];
                    })
                    ->prepend('N/A', '') 
                    ->toArray();
                })
                ->searchable()
                ->preload(false)
                ->createOptionForm([
                    // Fields matching your RecordStripeResource form
                    Forms\Components\TextInput::make('payment_id')
                        ->label('Payment Intent')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('payment_status')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('coupon_amount')
                        ->numeric(),
                    Forms\Components\TextInput::make('payment_amount')
                        ->required()
                        ->numeric()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('payment_amount', $state);
                        }),
                    Forms\Components\TextInput::make('released_amount')
                        ->numeric(),
                ]),
                    
                Forms\Components\TextInput::make('payment_amount')
                    ->label('Payment Amount')
                    ->numeric()
                    ->prefix('RM '),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                    
                    Tables\Columns\TextColumn::make('user.name'),
             
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Payment ID')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('payment_amount')
                    ->label('Amount')
                    ->money('USD')
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // This ensures the join_events_id is set automatically
                        // $data['join_events_id'] = $this->ownerRecord->id;
                        return $data;
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

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}