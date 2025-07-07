<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Drop unwanted columns if they exist
            if (Schema::hasColumn('games', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('games', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('games', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('games', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            
            // Add required columns if they don't exist
            if (!Schema::hasColumn('games', 'gameTitle')) {
                $table->string('gameTitle')->nullable();
            }
            if (!Schema::hasColumn('games', 'gameIcon')) {
                $table->string('gameIcon')->nullable();
            }
        });

        if (!Schema::hasTable('event_tier_prize')) {
            Schema::create('event_tier_prize', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_tier_id');
                $table->integer('position', false, true)->length(3); // int(3)
                $table->decimal('prize_sum', 10, 2);
                
                $table->foreign('event_tier_id')->references('id')->on('event_tier')->onDelete('cascade');
                
                $table->index(['event_tier_id', 'position']);
            });
        } 

        if (Schema::hasTable('event_join_results')) {
            Schema::table('event_join_results', function (Blueprint $table) {
                if (!Schema::hasColumn('event_join_results', 'prize_sum')) {
                    $table->decimal('prize_sum', 10, 2)->nullable();
                }
            });
        }

        if (Schema::hasTable('event_details')) {
            Schema::table('event_details', function (Blueprint $table) {
                if (!Schema::hasColumn('event_details', 'player_per_team')) {
                    $table->integer('player_per_team')->default(5)->nullable();
                }
                if (!Schema::hasColumn('event_details', 'games_per_match')) {
                    $table->integer('games_per_match')->default(3)->nullable();
                }
            });
        }

        if (Schema::hasTable('participant_coupons')) {
            Schema::table('participant_coupons', function (Blueprint $table) {
                if (!Schema::hasColumn('participant_coupons', 'type')) {
                    $table->string('type')->nullable();
                }
                if (!Schema::hasColumn('participant_coupons', 'discount_type')) {
                    $table->string('discount_type')->default('sum');
                }
            });
        }
        
        if (!Schema::hasTable('super_admins')) {

            Schema::create('super_admins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique('user_id');
            });

            $userId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'email' => 'superadmin@driftwood.gg',
                'password' => Hash::make('12345678'),
                'role' => 'ADMIN',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            DB::table('super_admins')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        if (!Schema::hasTable('csv_passwords')) {
            Schema::create('csv_passwords', function (Blueprint $table) {
                $table->id();
                $table->string('password');
            });

            // Insert the default password
            DB::table('csv_passwords')->insert([
                'password' => Hash::make('123456'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (Schema::hasColumn('games', 'gameTitle')) {
                $table->dropColumn('gameTitle');
            }
            if (Schema::hasColumn('games', 'gameIcon')) {
                $table->dropColumn('gameIcon');
            }
            
            if (!Schema::hasColumn('games', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('games', 'image')) {
                $table->string('image');
            }
            if (!Schema::hasColumn('games', 'created_at') && !Schema::hasColumn('games', 'updated_at')) {
                $table->timestamps();
            }
        });


        Schema::dropIfExists('event_tier_prize');
        
        if (Schema::hasTable('event_join_results')) {
            Schema::table('event_join_results', function (Blueprint $table) {
                if (Schema::hasColumn('event_join_results', 'prize_sum')) {
                    $table->dropColumn('prize_sum');
                }
            });
        }

        if (Schema::hasTable('event_details')) {
            Schema::table('event_details', function (Blueprint $table) {
                if (Schema::hasColumn('event_details', 'player_per_team')) {
                    $table->dropColumn('player_per_team');
                }
                if (Schema::hasColumn('event_details', 'games_per_match')) {
                    $table->dropColumn('games_per_match');
                }
            });
        }

        if (Schema::hasTable('participant_coupons')) {
            Schema::table('participant_coupons', function (Blueprint $table) {
                if (Schema::hasColumn('participant_coupons', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('participant_coupons', 'discount_type')) {
                    $table->dropColumn('discount_type');
                }
            });
        }


        Schema::dropIfExists('super_admins');

        DB::table('users')->where([
            'name' => 'Super Admin',
        ])->delete();

        Schema::dropIfExists('csv_passwords');

    }
};