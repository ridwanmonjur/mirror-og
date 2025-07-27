<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Io238\ISOCountries\Models\Country;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries_and_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Add unique constraint
            $table->string('emoji_flag');
            $table->enum('type', ['country', 'region'])->default('country');
            $table->integer('sort_order')->default(1000);
            
            $table->index(['sort_order']);
            $table->index(['type']);
            $table->index(['name', 'type']); // Composite index for better queries
        });

        // Insert regions first
        $regions = [
            // Asia
            ['name' => 'Global', 'emoji_flag' => '🌏', 'type' => 'region', 'sort_order' => 101],
            ['name' => 'Central Asia', 'emoji_flag' => '🏔️', 'type' => 'region', 'sort_order' => 2],
            ['name' => 'East Asia', 'emoji_flag' => '🏮', 'type' => 'region', 'sort_order' => 3],
            ['name' => 'Western Asia/Middle East', 'emoji_flag' => '🕌', 'type' => 'region', 'sort_order' => 4],
            ['name' => 'South Asia', 'emoji_flag' => '🏛️', 'type' => 'region', 'sort_order' => 5],
            ['name' => 'Southeast Asia', 'emoji_flag' => '🏝️', 'type' => 'region', 'sort_order' => 6],
            ['name' => 'North Asia', 'emoji_flag' => '🏔️', 'type' => 'region', 'sort_order' => 7],
            
            // Africa
            ['name' => 'North Africa', 'emoji_flag' => '🏺', 'type' => 'region', 'sort_order' => 12],
            ['name' => 'East Africa', 'emoji_flag' => '🦁', 'type' => 'region', 'sort_order' => 8],
            ['name' => 'West Africa', 'emoji_flag' => '🐘', 'type' => 'region', 'sort_order' => 9],
            ['name' => 'Southern Africa', 'emoji_flag' => '💎', 'type' => 'region', 'sort_order' => 10],
            ['name' => 'Central Africa', 'emoji_flag' => '🌿', 'type' => 'region', 'sort_order' => 11],
            
            // Americas
            ['name' => 'North America', 'emoji_flag' => '🍁', 'type' => 'region', 'sort_order' => 16],
            ['name' => 'Central America', 'emoji_flag' => '🌮', 'type' => 'region', 'sort_order' => 13],
            ['name' => 'Caribbean', 'emoji_flag' => '🏖️', 'type' => 'region', 'sort_order' => 14],
            ['name' => 'South America', 'emoji_flag' => '🦙', 'type' => 'region', 'sort_order' => 15],
            
            // Europe
            ['name' => 'Northern Europe', 'emoji_flag' => '❄️', 'type' => 'region', 'sort_order' => 21],
            ['name' => 'Western Europe', 'emoji_flag' => '🏛️', 'type' => 'region', 'sort_order' => 17],
            ['name' => 'Central Europe', 'emoji_flag' => '🏔️', 'type' => 'region', 'sort_order' => 18],
            ['name' => 'Southern Europe', 'emoji_flag' => '☀️', 'type' => 'region', 'sort_order' => 19],
            ['name' => 'Eastern Europe', 'emoji_flag' => '🌲', 'type' => 'region', 'sort_order' => 20],
            
            // Oceania
            ['name' => 'Australia & New Zealand', 'emoji_flag' => '🦘', 'type' => 'region', 'sort_order' => 24],
            ['name' => 'Polynesia', 'emoji_flag' => '🌺', 'type' => 'region', 'sort_order' => 22],
            ['name' => 'Melanesia', 'emoji_flag' => '🏝️', 'type' => 'region', 'sort_order' => 23],
            
            // Polar Regions
            ['name' => 'Arctic', 'emoji_flag' => '🐻‍❄️', 'type' => 'region', 'sort_order' => 26],
        ];

        // Add timestamps to regions if you added timestamps to the table

        DB::table('countries_and_regions')->insert($regions);

            $countries = Country::all();
            
                $countryData = [];
                $sortOrder = 1000;
                
                foreach ($countries as $country) {
                    $countryData[] = [
                        'name' => $country->name,
                        'emoji_flag' => $country->emoji_flag,
                        'type' => 'country',
                        'sort_order' => $sortOrder++,
                    ];
                }
                
                // Batch insert all countries at once
                DB::table('countries_and_regions')->insert($countryData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries_and_regions');
    }
};