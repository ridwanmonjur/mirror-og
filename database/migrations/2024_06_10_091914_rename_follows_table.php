<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename follows table to organizer_follows if organizer_follows doesn't exist
        if (!Schema::hasTable('organizer_follows')) {
            Schema::rename('organizer_follows', 'organizer_follows');
        }

        // Create participant_follows table if it doesn't exist
        if (!Schema::hasTable('participant_follows')) {
            Schema::create('participant_follows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('participant1_user');
                $table->unsignedBigInteger('participant2_user');
                $table->timestamps(); // shorthand for created_at and updated_at columns
                $table->unique(['participant1_user', 'participant2_user']);
                $table->foreign('participant1_user')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('participant2_user')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Drop columns if they exist in organizers table
        if (Schema::hasColumn('organizers', 'backgroundColor')) {
            Schema::table('organizers', function (Blueprint $table) {
                $table->dropColumn('backgroundColor');
            });
        }

        if (Schema::hasColumn('organizers', 'backgroundBanner')) {
            Schema::table('organizers', function (Blueprint $table) {
                $table->dropColumn('backgroundBanner');
            });
        }

        // Drop and add columns if they exist in participants table
        if (Schema::hasColumn('participants', 'backgroundColor')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('backgroundColor');
            });
        }

        if (Schema::hasColumn('participants', 'backgroundBanner')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropColumn('backgroundBanner');
            });
        }

        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'isAgeVisible')) {
                $table->boolean('isAgeVisible')->default(true);
            }
        });

        // Drop backgroundColor column if it exists in teams table
        if (Schema::hasColumn('teams', 'backgroundColor')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('backgroundColor');
            });
        }

        // Create user_profile table
        if (!Schema::hasTable('user_profile')) {
            Schema::create('user_profile', function (Blueprint $table) {
                $table->string('backgroundColor')->nullable();
                $table->string('backgroundBanner')->nullable();
                $table->string('backgroundGradient')->nullable();
                $table->string('fontColor')->nullable();
                $table->string('frameColor')->nullable();
            });
        }

        // Create team_profile table
        if (!Schema::hasTable('team_profile')) {
            Schema::create('team_profile', function (Blueprint $table) {
                $table->string('backgroundColor')->nullable();
                $table->string('backgroundBanner')->nullable();
                $table->string('backgroundGradient')->nullable();
                $table->string('fontColor')->nullable();
                $table->string('frameColor')->nullable();
            });
        }
    }

    public function down()
    {
        // Rename organizer_follows table back to follows
        if (Schema::hasTable('organizer_follows')) {
            Schema::rename('organizer_follows', 'organizer_follows');
        }

        // Add columns back to organizers table
        Schema::table('organizers', function (Blueprint $table) {
            if (!Schema::hasColumn('organizers', 'backgroundColor')) {
                $table->string('backgroundColor')->nullable();
            }
            if (!Schema::hasColumn('organizers', 'backgroundBanner')) {
                $table->string('backgroundBanner')->nullable();
            }
        });

        // Add and remove columns in participants table
        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'backgroundColor')) {
                $table->string('backgroundColor')->nullable();
            }
            if (!Schema::hasColumn('participants', 'backgroundBanner')) {
                $table->string('backgroundBanner')->nullable();
            }
            if (Schema::hasColumn('participants', 'isAgeVisible')) {
                $table->dropColumn('isAgeVisible');
            }
        });

        // Add columns back to teams table
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'backgroundColor')) {
                $table->string('backgroundColor')->nullable();
            }
        });

        // Drop participant_follows, user_profile, and team_profile tables if they exist
        if (Schema::hasTable('participant_follows')) {
            Schema::dropIfExists('participant_follows');
        }

        if (Schema::hasTable('user_profile')) {
            Schema::dropIfExists('user_profile');
        }

        if (Schema::hasTable('team_profile')) {
            Schema::dropIfExists('team_profile');
        }
    }
};
