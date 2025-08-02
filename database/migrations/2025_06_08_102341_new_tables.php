<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('saved_cards')) {

            Schema::create('saved_cards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('stripe_payment_method_id');
                $table->string('brand'); // visa, mastercard, etc.
                $table->string('last4', 4);
                $table->integer('exp_month');
                $table->string('fingerprint');

                $table->integer('exp_year');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->unique(['user_id', 'fingerprint']); // Ensure one card per user

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_default']);
            });
        }

        if (! Schema::hasTable('saved_payments')) {

            Schema::create('saved_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_intent_id')->unique();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('saved_card_id')->nullable();
                $table->string('currency', 3); // USD, MYR, etc.
                $table->string('status'); // succeeded, failed, pending, etc.
                $table->decimal('amount'); // amount in cents
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('saved_card_id')->references('id')->on('saved_cards')->onDelete('set null');
                $table->index(['user_id', 'status']);
                $table->index('payment_intent_id');
            });
        }

        if (Schema::hasTable('stripe_transactions')) {
            if (! Schema::hasColumn('stripe_transactions', 'user_id')) {
                Schema::table('stripe_transactions', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable();

                    try {
                        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    } catch (Exception $e) {
                        // Already exists, continue
                    }

                    if (! Schema::hasIndex('stripe_transactions', ['user_id'])) {
                        $table->index(['user_id']);
                    }

                });

                Schema::table('stripe_transactions', function (Blueprint $table) {
                    if (! Schema::hasColumn('stripe_transactions', 'brand')) {
                        $table->string('brand'); // visa, mastercard, etc.
                    }

                    if (! Schema::hasColumn('stripe_transactions', 'last4')) {
                        $table->string('last4', 4);
                    }

                    if (! Schema::hasColumn('stripe_transactions', 'currency')) {
                        $table->string('currency', 14);
                    }

                    if (! Schema::hasColumn('stripe_transactions', 'exp_month')) {
                        $table->integer('exp_month');
                    }

                    if (! Schema::hasColumn('stripe_transactions', 'exp_year')) {
                        $table->integer('exp_year');
                    }

                    if (! Schema::hasColumn('stripe_transactions', 'metadata')) {
                        $table->json('metadata')->nullable();
                    }
                });

            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('saved_payments');
        Schema::dropIfExists('saved_cards');
        if (Schema::hasTable('stripe_transactions')) {
            Schema::table('stripe_transactions', function (Blueprint $table) {

                try {
                    $table->dropForeign(['user_id']);
                } catch (Exception $e) {

                }
                if (Schema::hasIndex('stripe_transactions', ['user_id'])) {
                    $table->dropIndex(['user_id']);
                }

                // Drop foreign key and index first if user_id column exists
                if (Schema::hasColumn('stripe_transactions', 'user_id')) {
                    $table->dropColumn('user_id');
                }

                if (Schema::hasColumn('stripe_transactions', 'brand')) {
                    $table->dropColumn('brand');
                }

                if (Schema::hasColumn('stripe_transactions', 'last4')) {
                    $table->dropColumn('last4');
                }

                if (Schema::hasColumn('stripe_transactions', 'exp_month')) {
                    $table->dropColumn('exp_month');
                }

                if (Schema::hasColumn('stripe_transactions', 'currency')) {
                    $table->dropColumn('currency');
                }

                if (Schema::hasColumn('stripe_transactions', 'exp_year')) {
                    $table->dropColumn('exp_year');
                }

                if (Schema::hasColumn('stripe_transactions', 'metadata')) {
                    $table->dropColumn('metadata');
                }
            });
        }

    }
};
