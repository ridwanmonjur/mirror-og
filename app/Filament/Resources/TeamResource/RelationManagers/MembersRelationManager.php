<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;


class MembersRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'members';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'left' => 'Left',
                    ])
                    ->default('pending')
                    ->required(),
                
                Forms\Components\Select::make('actor')
                    ->options([
                        'team' => 'Team',
                        'user' => 'User',
                    ])
                    ->default('user')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'id'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'gray' => 'left',
                    ]),


                Tables\Columns\TextColumn::make('actor')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'left' => 'Left',
                    ]),
                
                Tables\Filters\SelectFilter::make('actor')
                    ->options([
                        'team' => 'Team',
                        'user' => 'User',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add Team Member'),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'accepted'])),
                
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('accept_selected')
                        ->label('Accept Selected')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'accepted'])),
                    
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'rejected'])),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}