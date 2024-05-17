<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'domain', 'backgroundBanner', 'bio', 'age',
                'region',
            ]);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->string('nickname')->nullable();
            $table->string('domain')->nullable();
            $table->string('backgroundBanner')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('region')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable();
            $table->string('domain')->nullable();
            $table->string('backgroundBanner')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('region')->nullable();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'domain', 'backgroundBanner',
                'bio', 'age', 'region',
            ]);
        });
    }
};
