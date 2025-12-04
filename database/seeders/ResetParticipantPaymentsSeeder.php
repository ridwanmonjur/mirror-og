<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\TransactionHistory;

class ResetParticipantPaymentsSeeder extends Seeder
{
    /**
     * Reset all participant payments and recreate them so the sum equals tierEntryFee
     * for each team with confirmed join_status
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('========================================');
        $this->command->info('Resetting Participant Payments');
        $this->command->info('========================================');

        DB::beginTransaction();

        try {
            // Step 1: Delete all existing participant payments
            $this->command->info('Deleting all existing participant payments...');
            $deletedPayments = DB::table('participant_payments')->delete();
            $this->command->info("✓ Deleted {$deletedPayments} participant payments.");

            // Step 2: Delete related transaction histories (optional - uncomment if needed)
            // $deletedTransactions = DB::table('transaction_histories')
            //     ->where('type', 'LIKE', '%Event Entry Fee%')
            //     ->delete();
            // $this->command->info("✓ Deleted {$deletedTransactions} transaction histories.");

            // Step 3: Get all confirmed join events
            $confirmedJoinEvents = JoinEvent::where('join_status', 'confirmed')
                ->with([
                    'eventDetails.tier',
                    'eventDetails.type',
                    'eventDetails.game',
                    'eventDetails.signup',
                    'roster.user',
                    'team'
                ])
                ->get();

            $this->command->info("\nFound {$confirmedJoinEvents->count()} confirmed join events.");
            $this->command->info('Creating new participant payments...');

            $processedCount = 0;
            $skippedCount = 0;
            $totalPaymentsCreated = 0;

            foreach ($confirmedJoinEvents as $joinEvent) {
                $eventDetails = $joinEvent->eventDetails;

                if (!$eventDetails || !$eventDetails->tier) {
                    $this->command->warn("⚠ Join Event ID {$joinEvent->id}: Missing event details or tier. Skipping.");
                    $skippedCount++;
                    continue;
                }

                // Determine the entry fee based on registration time
                $regStatus = $joinEvent->register_time ?? $eventDetails->getRegistrationStatus();
                $tierEntryFee = $regStatus == config('constants.SIGNUP_STATUS.EARLY')
                    ? (float) $eventDetails->tier->earlyEntryFee
                    : (float) $eventDetails->tier->tierEntryFee;

                // Get roster members
                $rosterMembers = $joinEvent->roster;

                if ($rosterMembers->isEmpty()) {
                    $this->command->warn("⚠ Join Event ID {$joinEvent->id}: No roster members. Skipping.");
                    $skippedCount++;
                    continue;
                }

                // Get team member IDs
                $teamMemberIds = $rosterMembers->pluck('team_member_id')->filter()->toArray();

                if (empty($teamMemberIds)) {
                    $this->command->warn("⚠ Join Event ID {$joinEvent->id}: No valid team members. Skipping.");
                    $skippedCount++;
                    continue;
                }

                // Distribute entry fee equally among team members
                $memberCount = count($teamMemberIds);
                $amountPerMember = round($tierEntryFee / $memberCount, 2);
                $distributedAmount = 0;

                $this->command->info("\n  Join Event ID {$joinEvent->id} (Team: {$joinEvent->team->teamName}):");
                $this->command->info("    Entry Fee: RM{$tierEntryFee}");
                $this->command->info("    Members: {$memberCount}");
                $this->command->info("    Amount per Member: RM{$amountPerMember}");

                $teamMemberIdsArray = array_values($teamMemberIds);

                foreach ($teamMemberIdsArray as $index => $memberId) {
                    $rosterMember = $rosterMembers->where('team_member_id', $memberId)->first();

                    if (!$rosterMember || !$rosterMember->user) {
                        $this->command->warn("    ⚠ Member ID {$memberId}: Missing user data. Skipping.");
                        continue;
                    }

                    // For the last member, calculate exact remaining amount to avoid rounding errors
                    $paymentAmount = ($index === $memberCount - 1)
                        ? round($tierEntryFee - $distributedAmount, 2)
                        : $amountPerMember;

                    // Ensure payment amount is positive
                    if ($paymentAmount <= 0) {
                        continue;
                    }

                    $distributedAmount += $paymentAmount;

                    // Create transaction history
                    $transaction = new TransactionHistory([
                        'name' => $eventDetails->eventName,
                        'type' => 'Event Entry Fee',
                        'link' => route('public.event.view', ['id' => $eventDetails->id]),
                        'amount' => $paymentAmount,
                        'summary' => "{$eventDetails->game->gameTitle}, {$eventDetails->tier->eventTier}, {$eventDetails->type->eventType}",
                        'isPositive' => false,
                        'date' => $joinEvent->updated_at ?? now(),
                        'user_id' => $rosterMember->user_id,
                    ]);
                    $transaction->save();

                    // Create participant payment
                    ParticipantPayment::create([
                        'team_members_id' => $memberId,
                        'user_id' => $rosterMember->user_id,
                        'join_events_id' => $joinEvent->id,
                        'payment_amount' => $paymentAmount,
                        'register_time' => $regStatus,
                        'history_id' => $transaction->id,
                        'type' => 'seeded',
                    ]);

                    $totalPaymentsCreated++;
                }

                $this->command->info("    ✓ Distributed: RM{$distributedAmount}");
                $processedCount++;
            }

            // Step 4: Summary
            $this->command->info("\n========================================");
            $this->command->info('SUMMARY');
            $this->command->info('========================================');
            $this->command->info("Total Confirmed Join Events: {$confirmedJoinEvents->count()}");
            $this->command->info("Processed: {$processedCount}");
            $this->command->info("Skipped: {$skippedCount}");
            $this->command->info("Payments Created: {$totalPaymentsCreated}");
            $this->command->info('========================================');

            DB::commit();
            $this->command->info("\n✓ All changes committed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\n✗ ERROR: {$e->getMessage()}");
            $this->command->error("Stack trace:");
            $this->command->error($e->getTraceAsString());
            $this->command->error("\nAll changes have been rolled back.");
        }
    }
}
