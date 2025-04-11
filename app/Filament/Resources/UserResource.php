<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Participant;
use App\Models\Organizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Common user fields
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('mobile_no')
                            ->maxLength(255),
                        TextInput::make('demo_email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('recovery_email')
                            ->email()
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at'),
                        FileUpload::make('userBanner')
                            ->image()
                            ->directory('images/user') 
                            ->maxSize(5120)
                            ->deleteUploadedFileUsing(function ($file, $livewire) {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                                
                                if ($livewire->record) {
                                    $livewire->record->userBanner = null;
                                    $livewire->record->save();
                                }
                                
                                return null;
                            })
                            ->saveUploadedFileUsing(function ($state, $file, callable $set, $livewire) {
                                // Custom file naming logic here
                                $oldBanner = $livewire->record->userBanner;

                                $newFilename = 'userBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                                $directory = 'images/user';
                                
                                $path = $file->storeAs($directory, $newFilename, 'public');
                                $livewire->record->userBanner = $path;
                                $livewire->record->save();
                                if ($oldBanner && $oldBanner !== $state) {
                                    $livewire->record->destroyUserBanner($oldBanner);
                                }
                                return $path;
                            }),
                           
                        Select::make('role')
                            ->options([
                                'ADMIN' => 'Admin',
                                'PARTICIPANT' => 'Participant',
                                'ORGANIZER' => 'Organizer',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Clear related fields when role changes
                                if ($state === 'ADMIN') {
                                    $set('participant_data', null);
                                    $set('organizer_data', null);
                                }
                            }),
                    ]),

                // Participant-specific fields
                Section::make('Participant Information')
                    ->schema([
                        TextInput::make('participant.nickname')
                            ->maxLength(255),
                        TextInput::make('participant.domain')
                            ->maxLength(255),
                        Textarea::make('participant.bio'),
                        TextInput::make('participant.age')
                            ->numeric(),
                        TextInput::make('participant.region')
                            ->maxLength(255),
                        DatePicker::make('participant.birthday'),
                        TextInput::make('participant.region_name')
                            ->default('')
                            ->maxLength(255),
                        TextInput::make('participant.region_flag')
                            ->maxLength(255),
                        Toggle::make('participant.isAgeVisible')
                            ->default(true),
                    ])
                    ->visible(fn (callable $get) => $get('role') === 'PARTICIPANT'),

                // Organizer-specific fields
                Section::make('Organizer Information')
                    ->schema([
                        TextInput::make('organizer.companyName')
                            ->maxLength(255),
                        Textarea::make('organizer.companyDescription')
                            ->maxLength(255),
                        TextInput::make('organizer.industry')
                            ->maxLength(255),
                        TextInput::make('organizer.type')
                            ->maxLength(255),
                        TextInput::make('organizer.website_link')
                            ->maxLength(255)
                            ->url(),
                        TextInput::make('organizer.instagram_link')
                            ->maxLength(255)
                            ->url(),
                        TextInput::make('organizer.facebook_link')
                            ->maxLength(255)
                            ->url(),
                        TextInput::make('organizer.twitter_link')
                            ->maxLength(255)
                            ->url(),
                        TextInput::make('organizer.stripe_customer_id')
                            ->maxLength(255),
                    ])
                    ->visible(fn (callable $get) => $get('role') === 'ORGANIZER'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('userBanner'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'ADMIN' => 'Admin',
                        'PARTICIPANT' => 'Participant',
                        'ORGANIZER' => 'Organizer',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}