<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventCategory;
use App\Models\EventTier;
use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\RecordStripe;
use App\Models\RosterMember;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<\App\Models\JoinEvent>
 */
final class JoinEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JoinEvent::class;

    public function definition(): array
    {
        return [];
    }

 

    public function seed($options = [
        'event' => [
            'eventTier' => 'Dolphin',
            'eventName' => 'Test Brackets',
            'eventType' => 'Tournament',
            'eventGame' => 'Dota 2',
        ],
        'joinEvent' => [
            'join_status' => 'confirmed',
            'payment_status' => 'confirmed',
            'participantPayment' => [
                'register_time' => null,
                'type' => 'wallet',
            ],
        ],
        'noOfConTeams' => 2,
    ])
    {
        // Store the events and teams
        $events = [];
        $organizers = [];
        $eventFactory = new EventDetailFactory;
        $stripeConnection = new StripeConnection;

        $result = $eventFactory->seed(0, $options['event']);
        $events[] = $result['event'];
        $organizers[] = $result['organizer'];

        $playersPerTeam = 5;
        $eventGame = $options['event']['eventGame'] ?? 'Dota 2';
        
        $eventCategory = EventCategory::where('gameTitle', $eventGame)->first();
        if ($eventCategory) {
            $playersPerTeam = $eventCategory->player_per_team ?? 5;
        }

        // Clear cache to ensure we get fresh tier data
        \Illuminate\Support\Facades\Cache::flush();
        EventTier::clearBootedModels();

        $eventTier = EventTier::where('eventTier', $options['event']['eventTier'])->first();
        if ($eventTier) {
            $tierTeamSlot = (int) $eventTier->tierTeamSlot;
            $noOfConTeams = (int) $options['noOfConTeams'];

            if ($noOfConTeams > $tierTeamSlot) {
                $options['noOfConTeams'] = $tierTeamSlot;
            }
        }

        JoinEvent::whereIn('event_details_id', collect($events)->pluck('id'))->delete();

        $numberOfUsers = ($options['noOfConTeams'] * $playersPerTeam)  ?? 80;

        $eventGame = $options['event']['eventGame'] ?? 'Dota 2';

        $teamMemberFactory = new TeamMemberFactory;
        $teamResult = $teamMemberFactory->seed($numberOfUsers, $playersPerTeam, $eventGame);
        $teams = $teamResult['teams'];
        $members = $teamResult['members'];
        foreach ($teams as $team) {
            $team->load(['user', 'user.participant']);
        }

        $amount = 5;

        $participantPaymentOption = $options['joinEvent']['participantPayment'];

        $joinEvents = [];
        foreach ($events as $event) {
            foreach ($teams as $team) {

                $joinEvent = JoinEvent::updateOrCreate([
                    'event_details_id' => $event->id,
                    'team_id' => $team->id,
                ],

                    [
                        'team_id' => $team->id,
                        'joiner_id' => $team->creator_id,
                        'joiner_participant_id' => $team->user->participant->id,
                        'payment_status' => $options['joinEvent']['payment_status'],
                        'register_time' => $participantPaymentOption['register_time'],
                        'join_status' => $options['joinEvent']['join_status'],
                        'vote_ongoing' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                $joinEvents[] = $joinEvent;
            }
        }

        foreach ($joinEvents as $joinIndex => $joinEvent) {

            foreach ($members as $teamMember) {
                if ($teamMember->team_id == $joinEvent->team_id) {
                    RosterMember::updateOrCreate([
                        'user_id' => $teamMember->user_id,
                        'join_events_id' => $joinEvent->id,
                        'team_member_id' => $teamMember->id,
                    ], [
                        'team_id' => $joinEvent->team_id,
                        'vote_to_quit' => false,
                    ]);

                    // First, check if participant payment already exists
                    $existingParticipantPayment = ParticipantPayment::where([
                        'team_members_id' => $teamMember->id,
                        'user_id' => $teamMember->user_id,
                        'join_events_id' => $joinEvent->id,
                    ])->first();

                    $transaction = null;
                    $stripeIntent = null;
                    $recordStripe = null;

                    $transactionData = [
                        'amount' => $amount,
                        'isPositive' => false,
                        'date' => now(),
                        'user_id' => $teamMember->user_id,
                    ];

                    if ($existingParticipantPayment) {
                        if ($existingParticipantPayment->history_id) {
                            $transaction = TransactionHistory::find($existingParticipantPayment->history_id);
                        }

                        if ($existingParticipantPayment->payment_id) {
                            $recordStripe = RecordStripe::find($existingParticipantPayment->payment_id);
                        }
                    }

                    if ($participantPaymentOption['type'] == 'wallet') {
                        if ($participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NORMAL')) {
                            $transactionDetails = [
                                'name' => "{$event->eventName}",
                                'type' => "Top up: RM {$amount}",
                                'link' => null,
                                'summary' => 'Wallet Normal Test Payment',
                            ];
                        } else {
                            $transactionDetails = [
                                'name' => "{$event->eventName}",
                                'type' => "Top up for Event: RM $amount",
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => 'Wallet Early Test Payment',
                            ];
                        }

                        if ($transaction) {
                            $transaction->update(array_merge($transactionData, $transactionDetails));
                        } else {
                            $transaction = new TransactionHistory(array_merge($transactionData, $transactionDetails));
                            $transaction->save();
                        }

                    } else {

                        if ($recordStripe && $recordStripe->payment_id) {
                            try {
                                $stripeIntent = $stripeConnection->retrieveStripePaymentByPaymentId($recordStripe->payment_id);

                                $stripeIntent = $stripeConnection->updatePaymentIntent($recordStripe->payment_id, [
                                    'amount' => $amount * 100,
                                    'metadata' => [
                                        'joinEventId' => $joinEvent->id,
                                        'memberId' => $joinEvent->teams_id,
                                        'teamId' => $teamMember->id,
                                    ],
                                ]);
                            } catch (\Exception $e) {
                                $stripeIntent = $stripeConnection->createPaymentIntent([
                                    'amount' => $amount * 100,
                                    'currency' => 'myr',
                                    'capture_method' => $participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NORMAL') ?
                                        'manual' : 'automatic',
                                    'metadata' => [
                                        'joinEventId' => $joinEvent->id,
                                        'memberId' => $joinEvent->teams_id,
                                        'teamId' => $teamMember->id,
                                    ],
                                ]);
                            }
                        } else {
                            $stripeIntent = $stripeConnection->createPaymentIntent([
                                'amount' => $amount * 100,
                                'currency' => 'myr',
                                'capture_method' => $participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NORMAL') ?
                                    'manual' : 'automatic',
                                'metadata' => [
                                    'joinEventId' => $joinEvent->id,
                                    'memberId' => $joinEvent->teams_id,
                                    'teamId' => $teamMember->id,
                                ],
                            ]);
                        }

                        if ($recordStripe) {
                            $recordStripe->update([
                                'payment_id' => $stripeIntent['id'],
                                'payment_status' => $stripeIntent['status'],
                                'payment_amount' => $amount,
                                'updated_at' => now(),
                            ]);
                        } else {
                            $recordStripe = RecordStripe::create([
                                'payment_id' => $stripeIntent['id'],
                                'payment_status' => $stripeIntent['status'],
                                'payment_amount' => $amount,
                                'created_at' => now(),
                            ]);
                        }

                        // Set transaction details based on register_time
                        if ($participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NORMAL')) {
                            $transactionDetails = [
                                'name' => "{$event->eventName}",
                                'type' => 'Event Entry Fee Hold',
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => 'Stripe Normal Test Payment',
                            ];
                        } else {
                            $transactionDetails = [
                                'name' => "Entry Fee: RM {$event->eventName}",
                                'type' => 'Event Entry Fee',
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => 'Stripe Early Test Payment',
                            ];
                        }

                        if ($transaction) {
                            $transaction->update(array_merge($transactionData, $transactionDetails));
                        } else {
                            $transaction = new TransactionHistory(array_merge($transactionData, $transactionDetails));
                            $transaction->save();
                        }
                    }

                    ParticipantPayment::updateOrCreate([
                        'team_members_id' => $teamMember->id,
                        'user_id' => $teamMember->user_id,
                        'join_events_id' => $joinEvent->id,
                    ], [
                        'payment_amount' => $amount,
                        'payment_id' => isset($recordStripe) ? $recordStripe->id : null,
                        'register_time' => $participantPaymentOption['register_time'],
                        'history_id' => $transaction->id,
                        'type' => $participantPaymentOption['type'],
                    ]);
                }
            }

            DB::table('event_join_results')->updateOrInsert(
                ['join_events_id' => $joinEvent->id],
                ['position' => $joinIndex + 1]
            );
        }

        return [
            'events' => $events,
            'joinEvents' => $joinEvents,
            'organizer' => $organizers,
            ...$teamResult,
        ];
    }
}
