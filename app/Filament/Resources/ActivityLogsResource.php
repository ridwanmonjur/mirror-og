<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogsResource\Pages;
use App\Filament\Resources\ActivityLogsResource\RelationManagers;
use App\Models\ActivityLogs;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityLogsResource extends Resource
{
    protected static ?string $model = ActivityLogs::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\Textarea::make('log')
                    ->required()
                    ->columnSpanFull(),
                

                // Forms\Components\TextInput::make('subject_type')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\Select::make('subject_id')
                //     ->relationship('users', 'name')
                //     ->required(),
                // Forms\Components\TextInput::make('subject_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('object_type')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('object_id')
                //     ->numeric(),
                Forms\Components\Select::make('subject_type')
                ->label('Subject Type')
                ->options([
                    User::class => 'User',
                    \App\Models\JoinEvent::class => 'JoinEvent',
                    \App\Models\TeamMember::class => 'TeamMember'
                    // Add other possible morph types
                ])
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('subject_id', null)),

            Forms\Components\Select::make('subject_id')
                ->label('Subject')
                ->options(function (callable $get) {
                    $type = $get('subject_type');
                    if (!$type) return [];
                    
                    return $type::query()
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->reactive()
                ->searchable()
                ->preload()
                ->disabled(fn (callable $get) => !$get('subject_type')),

            Forms\Components\Select::make('object_type')
                ->label('Object Type')
            
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('object_id', null)),

            Forms\Components\Select::make('object_id')
                ->label('Object')
                ->options(function (callable $get) {
                    $type = $get('object_type');
                    if (!$type) return [];
                    
                    return $type::query()
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->reactive()
                ->searchable()
                ->preload()
                ->disabled(fn (callable $get) => !$get('object_type')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subject Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('subject.name') // Assuming related model has 'name' field
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('object_type')
                    ->label('Object Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('object.id')
                    ->label('Object')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('subject_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('subject_id')
                
                //     ->numeric()
                //     ->sortable(),
                
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
            'index' => Pages\ListActivityLogs::route('/'),
            'create' => Pages\CreateActivityLogs::route('/create'),
            'edit' => Pages\EditActivityLogs::route('/{record}/edit'),
        ];
    }
}
