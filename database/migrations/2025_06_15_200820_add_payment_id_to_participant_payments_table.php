<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organizer_payments')) {
            Schema::create('organizer_payments', function (Blueprint $table) {
                $table->id();
                $table->double('payment_amount')->nullable();
                $table->double('discount_amount')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('history_id')->nullable();
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('history_id')->references('id')->on('transaction_history')->onDelete('cascade');
                $table->foreign('payment_id')->references('id')->on('stripe_transactions')->onDelete('set null');

            });
        }

        if (Schema::hasColumn('participant_payments', 'payment_id')) {
            Schema::table('participant_payments', function (Blueprint $table) {
                $table->dropColumn('payment_id');
            });
        }

        if (Schema::hasColumn('event_details', 'payment_transaction_id')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payment_transaction_id');
            });
        }

        Schema::table('participant_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('stripe_transactions')->onDelete('set null');
        });

        Schema::table('event_details', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_transaction_id')->nullable();
            $table->foreign('payment_transaction_id')->references('id')->on('organizer_payments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('participant_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });
        Schema::table('participant_payments', function (Blueprint $table) {
            $table->string('payment_id')->nullable(); // Assuming original type was string
        });

        if (Schema::hasColumn('event_details', 'payment_transaction_id')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payment_transaction_id');
            });
        }

        Schema::table('event_details', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_transaction_id')->nullable();
            $table->foreign('payment_transaction_id')->references('id')->on('stripe_transactions')->onDelete('set null');
        });

        Schema::dropIfExists('organizer_payments');
    }
};
