<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class UserProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'profile';
    protected static bool $hasAssociatedRecord = true;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\ColorPicker::make('backgroundColor')
                    ->label('Background Color')
                    ->rgba(),
                Forms\Components\FileUpload::make('backgroundBanner')
                    ->image()
                    ->directory('images/user') 
                    ->maxSize(5120)
                    ->deleteUploadedFileUsing(function ($file, $livewire) {
                        if ($file && Storage::disk('public')->exists($file)) {
                            Storage::disk('public')->delete($file);
                        }

                        $owner = $livewire->getOwnerRecord();
                        $record = $owner->profile;
                        
                        if ($record) {
                            $record->backgroundBanner = null;
                            $record->save();
                        }
                        
                        return null;
                    })
                    ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $record = $owner->profile;
                        $oldBanner = $record->backgroundBanner;

                        $newFilename = 'backgroundBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                        $directory = 'images/user';
                        
                        $path = $file->storeAs($directory, $newFilename, 'public');
                        $record->backgroundBanner = $path;
                        $record->save();
                        if ($oldBanner && $oldBanner !== $state) {
                            $record->destroyUserBanner($oldBanner);
                        }
                        return $path;
                    }),
                Forms\Components\TextInput::make('backgroundGradient')
                    ->label('Background Gradient'),
                    
                Forms\Components\ColorPicker::make('fontColor')
                    ->label('Font Color')
                    ->rgba(),
                Forms\Components\ColorPicker::make('frameColor')
                    ->label('Frame Color')
                    ->rgba(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('backgroundColor')
                    ->label('Background Color'),
                Tables\Columns\ImageColumn::make('backgroundBanner'),
                Tables\Columns\ViewColumn::make('backgroundGradient')
                    ->label('Gradient')
                    ->view('filament.tables.columns.gradient-preview')
                    ->searchable(),

                Tables\Columns\ColorColumn::make('fontColor')
                    ->label('Font Color'),
                Tables\Columns\ColorColumn::make('frameColor')
                    ->label('Frame Color'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => !$this->getOwnerRecord()->profile()->exists())
                    // ->successRedirectUrl(fn () => $this->getParentResource()::getUrl('index'))
                    ->createAnother(false)
,
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