<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterestedUserResource\Pages;
use App\Mail\SendBetaWelcomeMail;
use App\Models\InterestedUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Traits\HandlesFilamentExceptions;
use App\Models\Participant;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InterestedUserResource extends Resource
{
    use HandlesFilamentExceptions;
    protected static ?string $model = InterestedUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Interested Users';

    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->displayFormat('Y-m-d h:i A') 
                    ->timezone('Asia/Kuala_Lumpur')
                    ->seconds(false)
                    ->native(false)
                    ->nullable(),
                TextInput::make('email_verified_token')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('pass_text')
                    ->label('Password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => $state)
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->dateTime('Y-m-d h:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d h:i A')
                    ->timezone('Asia/Kuala_Lumpur')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->bulkActions([
                BulkAction::make('send_invites')
                    ->label('Send Beta Invites')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Send Beta Invites')
                    ->modalDescription('Are you sure you want to send beta invites to the selected users?')
                    ->modalSubmitActionLabel('Send Invites')
                    ->action(function (Collection $records) {
                        self::sendBetaInvites($records);
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('not_verified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListInterestedUsers::route('/'),
            // 'create' => Pages\CreateInterestedUser::route('/create'),
            // 'edit' => Pages\EditInterestedUser::route('/{record}/edit'),
        ];
    }

    protected static function sendBetaInvites(Collection $records): void
    {
        try {
            $interestedUsers = DB::table('interested_user')
                ->whereIn('id', $records->pluck('id'))
                ->get();

            $interestedUserEmails = $interestedUsers->pluck('email')->toArray();
            $existingUsers = User::whereIn('email', $interestedUserEmails)->get();

            // Process existing users
            foreach ($existingUsers as $user) {
                $password = Str::random(8);
                
                // Find the corresponding interested user
                $interestedUser = $interestedUsers->firstWhere('email', $user->email);
                
                if ($interestedUser) {
                    DB::table('interested_user')
                        ->where('email', $user->email)
                        ->update([
                            'pass_text' => $password,
                            'email_verified_at' => now(),
                        ]);

                    $user->password = Hash::make($password);
                    $user->save();

                    Mail::to($user)->queue(new SendBetaWelcomeMail($user, $password));
                }
            }

            // Process new users
            $existingUsersEmails = $existingUsers->pluck('email')->toArray();
            $newEmails = array_diff($interestedUserEmails, $existingUsersEmails);

            foreach ($newEmails as $email) {
                $username = explode('@', $email)[0];
                $username = strlen($username) > 5 ? substr($username, 0, 5) : $username;
                $password = Str::random(8);
                
                DB::table('interested_user')
                    ->where('email', $email)
                    ->update([
                        'pass_text' => $password,
                        'email_verified_at' => now(),
                    ]);

                $user = User::create([
                    'email' => $email,
                    'password' => Hash::make($password),
                    'name' => Str::random(2) . $username . Str::random(2),
                    'role' => 'PARTICIPANT',
                    'email_verified_at' => now(),
                ]);

                Participant::create([
                    'user_id' => $user->id,
                ]);

                Mail::to($user)->queue(new SendBetaWelcomeMail($user, $password));
            }

            Notification::make()
                ->title('Beta invites sent successfully!')
                ->success()
                ->body('Created or updated ' . count($interestedUserEmails) . ' users')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error sending beta invites')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}