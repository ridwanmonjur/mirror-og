<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Traits\HandlesFilamentExceptions;

class NotificationCountRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;
    protected static string $relationship = 'notificationCount';
    
    // For one-to-one relationships
    protected static bool $shouldRenderTableWhenHasRecords = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('social_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Social Notifications'),
                    
                Forms\Components\TextInput::make('teams_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Team Notifications'),
                    
                Forms\Components\TextInput::make('event_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Event Notifications'),
                    
                // user_id is automatically handled by Filament
                // created_at and updated_at are handled automatically
            ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('social_count')
                    ->label('Social Notifications'),
                    
                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Team Notifications'),
                    
                Tables\Columns\TextColumn::make('event_count')
                    ->label('Event Notifications'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y — h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->dateTime('M d, Y — h:i A') 
                    ->sortable(),
            ])
            ->headerActions([
                // For one-to-one, show create only if no record exists
                Tables\Actions\CreateAction::make()
                    ->label('Set Notification Counts')
                    ->visible(fn () => !$this->getOwnerRecord()->notificationCount()->exists())
                    ->createAnother(false)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed for one-to-one
            ]);
    }
}