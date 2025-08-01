<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->string('report_id');
            $table->string('match_number');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('dispute_userId');
            $table->unsignedBigInteger('dispute_teamId');
            $table->string('dispute_teamNumber');
            $table->string('dispute_reason');
            $table->text('dispute_description')->nullable();

            $table->unsignedBigInteger('response_userId')->nullable();
            $table->unsignedBigInteger('response_teamId')->nullable();
            $table->string('response_teamNumber')->nullable();
            $table->text('response_explanation')->nullable();

            $table->unsignedBigInteger('resolution_winner')->nullable();
            $table->unsignedBigInteger('resolution_resolved_by')->nullable();

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('event_details')->onDelete('cascade');
            $table->foreign('dispute_userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign(columns: 'dispute_teamId')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('response_userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('response_teamId')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('resolution_winner')->references('id')->on('teams')->onDelete('cascade');

            $table->unique(columns: ['event_id', 'match_number', 'report_id']);

        });

        Schema::create('image_videos', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->enum('file_type', ['image', 'video']);
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();
        });

        Schema::create('dispute_image_video', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable');
            $table->foreignId('image_video_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['dispute', 'response']);
            // To distinguish between dispute and response media
            $table->timestamps();
        });

        Schema::dropIfExists('videos');

    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('dispute_image_video');
        Schema::dropIfExists('image_videos');

        if (! Schema::hasTable('videos')) {
            Schema::create('videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('filename');
                $table->string('path');
                $table->timestamps();
            });
        }
    }
};
