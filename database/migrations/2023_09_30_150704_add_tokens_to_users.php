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
            $table->string('otp')->nullable();
            $table->string('otp_method')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->string('email_verification_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('otp')->nullable();
            $table->dropColumn('otp_method')->nullable();
            $table->dropColumn('password_reset_token')->nullable();
            $table->dropColumn('email_verification_token')->nullable();
        });
    }
};
