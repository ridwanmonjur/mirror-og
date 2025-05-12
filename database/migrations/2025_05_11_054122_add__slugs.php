<?php
// database/migrations/2025_05_11_000001_add_slugs_to_multiple_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add slug to users table
        if (!Schema::hasColumn('users', 'slug')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('name');
            });
        }

        // Add slug to eventDetails table
        if (!Schema::hasColumn('event_details', 'slug')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('eventName');
            });
        }

        // Add slug to teamName table
        if (!Schema::hasColumn('teams', 'slug')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('teamName');
            });
        }

        // Generate slugs for existing data
        $this->generateSlugs('users', 'name');
        $this->generateSlugs('event_details', 'eventName');
        $this->generateSlugs('teams', 'teamName');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    /**
     * Generate unique slugs for existing records
     */
    private function generateSlugs($table, $nameColumn)
    {
        $records = DB::table($table)->whereNotNull($nameColumn)->get();
        
        foreach ($records as $record) {
            $baseSlug = Str::slug($record->$nameColumn);
            $slug = $baseSlug;
            $count = 1;
            
            // Ensure unique slug
            while (DB::table($table)->where('slug', $slug)->where('id', '!=', $record->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            
            DB::table($table)
                ->where('id', $record->id)
                ->update(['slug' => $slug]);
        }
    }
};