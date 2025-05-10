<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Participant;
use App\Models\Organizer;
use App\Models\NotificationCounter;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    public static function deleteRelatedTables() {
  
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables
        DB::table('users')->truncate();
        DB::table('organizers')->truncate();
        DB::table('participants')->truncate();
        DB::table('notification_counters')->truncate();
        DB::table('user_address')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('123456'), // password
            'remember_token' => Str::random(60),
            'role' => 'PARTICIPANT', // Default role
            'status' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Create appropriate related records based on role
            if ($user->role === 'PARTICIPANT') {
                Participant::create([
                    'user_id' => $user->id,
                    'nickname' => $this->faker->userName(),
                    'age' => $this->faker->numberBetween(13, 60),
                    'isAgeVisible' => 1,
                    'created_at' => $user->created_at,
                    'updated_at' => now(),
                ]);
            } elseif ($user->role === 'ORGANIZER') {
                Organizer::create([
                    'user_id' => $user->id,
                    'companyName' => $this->faker->company(),
                    'companyDescription' => $this->faker->paragraph(),
                    'created_at' => $user->created_at,
                    'updated_at' => now(),
                ]);
            }

            // Create notification counter for all users
            NotificationCounter::create([
                'user_id' => $user->id,
                'social_count' => 0,
                'teams_count' => 0,
                'event_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'ADMIN',
                'email' => 'admin@driftwood.gg',
                'name' => 'Admin',
            ];
        });
    }

    /**
     * Indicate that the user is an organizer.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function organizer()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'ORGANIZER',
                'name' => $this->faker->company() . ' Admin',
            ];
        });
    }

    /**
     * Indicate that the user is a participant.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function participant()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'PARTICIPANT',
                'name' => 'p_' . $this->faker->userName(),
            ];
        });
    }

    /**
     * Indicate that the user has Google authentication.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withGoogleAuth()
    {
        return $this->state(function (array $attributes) {
            return [
                'google_id' => $this->faker->numerify('####################'),
                'email_verified_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the user has a Stripe customer ID.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withStripeCustomer()
    {
        return $this->state(function (array $attributes) {
            return [
                'stripe_customer_id' => 'cus_' . Str::random(14),
            ];
        });
    }

    /**
     * Indicate that the user has an address.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAddress()
    {
        return $this->afterCreating(function (User $user) {
            Address::create([
                'user_id' => $user->id,
                'city' => $this->faker->city(),
                'addressLine1' => $this->faker->streetAddress(),
                'postcode' => $this->faker->postcode(),
                'country' => $this->faker->country(),
            ]);
        });
    }

    /**
     * Indicate that the user has a profile banner.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withProfileBanner()
    {
        return $this->state(function (array $attributes) {
            return [
                'userBanner' => 'images/user/userBanner-' . now()->timestamp . '.jpg',
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
                'email_verified_token' => Str::random(60),
                'email_verified_expires_at' => now()->addDays(1),
            ];
        });
    }
}