<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('teamName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('creator_id')
                    ->required()
                    ->relationship('user', 'name', 
                    fn ($query) => $query->where('role', 'PARTICIPANT') 
                ),
                Forms\Components\TextInput::make('teamDescription')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('teamBanner')
                    ->image()
                    ->directory('images/team') 
                    ->maxSize(5120)
                    ->deleteUploadedFileUsing(function ($file, $livewire) {
                        if ($file && Storage::disk('public')->exists($file)) {
                            Storage::disk('public')->delete($file);
                        }
                        
                        if ($livewire->record) {
                            $livewire->record->teamBanner = null;
                            $livewire->record->save();
                        }
                        
                        return null;
                    })
                    ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                        // Custom file naming logic here
                        $oldBanner = $livewire->record->userBanner;

                        $newFilename = 'teamBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                        $directory = 'images/team';
                        
                        $path = $file->storeAs($directory, $newFilename, 'public');
                        $livewire->record->teamBanner = $path;
                        $livewire->record->save();
                        if ($oldBanner && $oldBanner !== $state) {
                            $livewire->record->destroyTeanBanner($oldBanner);
                        }
                        return $path;
                    }),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_flag')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teamName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Team Creator')
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
                Tables\Columns\TextColumn::make('teamDescription')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('teamBanner'),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_flag')
                    ->searchable(),
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
            RelationManagers\TeamProfileRelationManager::class,
            RelationManagers\TeamCaptainRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
