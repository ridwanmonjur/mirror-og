<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\CountryRegion;
use App\Models\User;
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
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Filament\Traits\HandlesFilamentExceptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $navigationIcon = 'heroicon-o-identification';


    public static function form(Form $form): Form
    {
        $countries = [];
        
        foreach (CountryRegion::getAllCached() as $country) {
            $countries[$country->id] = $country->name;
        }

        $roleOptions = [
            'ADMIN' => 'Admin',
            'PARTICIPANT' => 'Participant',
            'ORGANIZER' => 'Organizer',
        ];

     

        return $form
            ->schema([
                Tabs::make('User Management')
                    ->tabs([
                        Tabs\Tab::make('User Information')
                            ->schema([
                                
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(fn (string $context): bool => $context === 'edit'),
                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(true),
                                TextInput::make('mobile_no')
                                    ->maxLength(255),
                                TextInput::make('demo_email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('recovery_email')
                                    ->email()
                                    ->maxLength(255),
                                DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->displayFormat('Y-m-d h:i A')
                                    ->native(false)
                                    ->timezone('Asia/Kuala_Lumpur'),
                                Forms\Components\Section::make('User Banner')
                                    ->description('Banner upload is only available when editing an existing item')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Forms\Components\Placeholder::make('create_notice')
                                        ->content('Please save the user first, then edit to upload a banner image.')
                                        ->visible(fn (string $context): bool => $context === 'create'),
                                        FileUpload::make('userBanner')
                                            ->image()
                                            ->directory('images/user') 
                                            ->maxSize(5120)
                                            ->visible(fn (string $context): bool => $context === 'edit')
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
                                                // Generate new filename regardless of creation or edit
                                                    $newFilename = 'userBanner-' . time() . '-' . auth()->id() . '.' . $file->getClientOriginalExtension();
                                                    $directory = 'images/user';
                                                    $path = $file->storeAs($directory, $newFilename, 'public');
                                                    
                                                    // For edit mode, handle the existing record
                                                    if ($livewire->record) {
                                                        $oldBanner = $livewire->record->userBanner;
                                                        $livewire->record->userBanner = $path;
                                                        $livewire->record->save();
                                                        
                                                        if ($oldBanner && $oldBanner !== $state) {
                                                            $livewire->record->destroyUserBanner($oldBanner);
                                                        }
                                                    }
                                                    
                                                    // Always return the path so it can be stored in the form data
                                                    return $path;

                                            }),
                                ]),
                                
                                Select::make('role')
                                    ->options($roleOptions)
                                    ->required()
                                    ->reactive()
                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                    ->afterStateUpdated(function ($state, callable $set, $livewire) {
                                        
                                        
                                        if ($state === 'ADMIN') {
                                            if ($livewire->record && $livewire->record->participant) {
                                                $livewire->record->participant->delete();
                                                $livewire->record->refresh();
                                            }
                                            
                                            if ($livewire->record && $livewire->record->organizer) {
                                                $livewire->record->organizer->delete();
                                                $livewire->record->refresh();
                                            }
                                            
                                            $set('participant.nickname', null);
                                            $set('organizer.companyName', null);
                                        } else if ($state === 'PARTICIPANT') {
                                            if ($livewire->record && $livewire->record->organizer) {
                                                $livewire->record->organizer->delete();
                                                $livewire->record->refresh();
                                            }
                                            
                                            // Clear organizer form fields
                                            $set('organizer.companyName', null);
                                        } else if ($state === 'ORGANIZER') {
                                            if ($livewire->record && $livewire->record->participant) {
                                                $livewire->record->participant->delete();
                                                $livewire->record->refresh();
                                            }
                                            
                                            // Clear participant form fields
                                            $set('participant.nickname', null);
                                        }
                                    })
                            ]),
                            
                        Tabs\Tab::make('Participant Profile')
                            ->schema([
                                Section::make('Participant Details')
                                    ->schema([
                                        TextInput::make('participant.nickname')
                                            ->maxLength(255),
                                        TextInput::make('participant.domain')
                                            ->maxLength(255),
                                        Textarea::make('participant.bio'),
                                        TextInput::make('participant.age')
                                            ->numeric(),
                                        DatePicker::make('participant.birthday'),
                                        Select::make('participant.region')
                                            ->label('Country')
                                            ->options($countries)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                                if ($state) {
                                                    $country = CountryRegion::find($state);
                                                    if ($country) {
                                                        $set('participant.region_name', $country['name']);
                                                        $set('participant.region_flag', $country['emoji_flag']);
                                                    } else {
                                                        $set('participant.region_name', null);
                                                        $set('participant.region_flag', null);
                                                    }
                                                }
                                            })
                                            ->searchable()
                                            ->placeholder('Select a country'),
                                        Forms\Components\Hidden::make('participant.region_name'),
                                        Forms\Components\Hidden::make('participant.region_flag'),
                                        Toggle::make('participant.isAgeVisible')->default(true)
                                    ])
                            ])
                            ->visible(fn (callable $get) => $get('role') === 'PARTICIPANT'),
                            
                        Tabs\Tab::make('Organizer Profile')
                            ->schema([
                                Section::make('Organizer Details')
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
                                            ->url(true),
                                        TextInput::make('organizer.instagram_link')
                                            ->maxLength(255)
                                            ->url(true),
                                        TextInput::make('organizer.facebook_link')
                                            ->maxLength(255)
                                            ->url(true),
                                        TextInput::make('organizer.twitter_link')
                                            ->maxLength(255)
                                            ->url(true),
                                        TextInput::make('organizer.stripe_customer_id')
                                            ->maxLength(255)
                                            ->disabled(),
                                    ])
                            ])
                            ->visible(fn (callable $get) => $get('role') === 'ORGANIZER'),
                    ]),
            ]);
    }

   


    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\ImageColumn::make('userBanner')
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/404q.png'))
                    ->extraImgAttributes([
                            'class' => 'border border-gray-300 dark:border-gray-600',
                        ])
                    ->size(60),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
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
            ->deferLoading()
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
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserProfileRelationManager::class,
            RelationManagers\AddressRelationManager::class,
            // RelationManagers\ActivityLogsRelationManager::class,
            RelationManagers\NotificationCountRelationManager::class,
            RelationManagers\NotificationListRelationManager::class,
            
            RelationManagers\DiscountsRelationManager::class,


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