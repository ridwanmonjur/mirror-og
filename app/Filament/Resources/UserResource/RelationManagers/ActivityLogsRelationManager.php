<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogsRelationManager extends RelationManager
{
    use HandlesFilamentExceptions;

    protected static string $relationship = 'activities';

    public function form(Form $form): Form
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
                Forms\Components\TextInput::make('object_type')
                    ->label('Object Type'),
                Forms\Components\TextInput::make('object_id')
                    ->label('Object'),
            ]);
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'id'),

                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/404q.png'))
                    ->extraImgAttributes([
                        'class' => 'border border-gray-300 dark:border-gray-600',
                    ])
                    ->size(60),
                Tables\Columns\TextColumn::make('object_type')
                    ->label('Object Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('object.name')
                    ->label('Object')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('log'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y — h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable(),
            ])
            // ->filters([
            //     //
            // ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
