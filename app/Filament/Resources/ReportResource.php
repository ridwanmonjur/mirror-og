<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reporter_id')
                    ->relationship('reporter', 'name')
                    ->searchable()
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                    ->required(),
                Forms\Components\Select::make('reported_user_id')
                    ->relationship('reportedUser', 'name')
                    ->label('Reported User')
                    ->searchable()
                    ->different('reporter_id')
                    ->optionsLimit(10)
                    ->searchDebounce(500)
                  
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),

                Tables\Columns\TextColumn::make('reporter.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportedUser.name')
                    ->label('Reported User')
                    ->numeric()
                    ->sortable(),
               
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->timezone('Asia/Kuala_Lumpur')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     //
            // ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            // 'create' => Pages\CreateReport::route('/create'),
            // 'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
