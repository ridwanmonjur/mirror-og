<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationListRelationManager extends RelationManager
{
    protected static string $relationship = 'notificationList';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'social' => 'Social',
                        'team' => 'Team',
                        'event' => 'Event',
                        // Add more notification types as needed
                    ]),
                    
                Forms\Components\TextInput::make('icon_type'),
                    
                Forms\Components\TextInput::make('img_src')
                    ->label('Image Source')
                    ->url()
                    ->maxLength(255),
                    
                Forms\Components\RichEditor::make('html')
                    ->label('Notification Content')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('link')
                    ->label('Notification Link')
                    ->url()
                    ->maxLength(255),
                    
                Forms\Components\Toggle::make('is_read')
                    ->label('Mark as Read')
                    ->default(false),
                    
                // user_id is automatically handled by Filament
                // created_at and updated_at are automatically managed
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'id'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => 
                        match ($state) {
                            'social' => 'success',
                            'team' => 'info',
                            'event' => 'warning',
                            default => 'gray',
                        }
                    )
                    ->searchable()
                    ->sortable(),
                    
              
                    
                Tables\Columns\TextColumn::make('html')
                    ->label('Content')
                    ->html()
                    ->limit(50),
                    
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc') // Latest notifications first
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'social' => 'Social',
                        'team' => 'Team',
                        'event' => 'Event',
                        // Add more as needed
                    ]),
                    
                Tables\Filters\Filter::make('unread')
                    ->query(fn (Builder $query): Builder => $query->where('is_read', false))
                    ->label('Unread Only')
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('mark_read')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn ($record) => $record->update(['is_read' => true]))
                    ->visible(fn ($record) => !$record->is_read),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_read_bulk')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}