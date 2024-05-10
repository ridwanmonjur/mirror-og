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
        Schema::table('awards_results', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->unsignedBigInteger('award_id');
            $table->foreign('award_id')->references('id')->on('awards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('awards_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId(['award_id']);
            $table->dropColumn('award_id');
            $table->string('title');
            $table->string('description');
        });
    }
};
