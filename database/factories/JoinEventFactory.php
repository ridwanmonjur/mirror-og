<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JoinEvent;
use App\Models\ParticipantPayment;
use App\Models\RecordStripe;
use App\Models\RosterMember;
use App\Models\StripeConnection;
use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

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

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'event_details_id' => \App\Models\EventDetail::factory(),
            'team_id' => \App\Models\Team::factory(),
            'joiner_id' => \App\Models\User::factory(),
            'joiner_participant_id' => fake()->randomNumber(),
            'payment_status' => fake()->randomElement(['pending', 'completed', 'waived']),
            'join_status' => fake()->randomElement(['canceled', 'confirmed', 'pending']),
            'vote_starter_id' => \App\Models\User::factory(),
            'vote_ongoing' => fake()->optional()->randomNumber(1),
            'roster_captain_id' => \App\Models\RosterMember::factory(),
        ];
    }


    public function seed($options = [
        'event' => [
            'eventTier' => 'Dolphin',
            'eventName' => 'Test Brackets'
        ],
        'joinEvent' => [
            'join_status' => 'confirmed',
            'payment_status' => 'confirmed',
            'participantPayment' => [
                'register_time' => null,
                'type' => 'wallet',
            ]
        ]
    ]) {
        // Store the events and teams
        $events = [];
        $organizers = [];
        $eventFactory = new EventDetailFactory();
        $stripeConnection = new StripeConnection();
        
        $result = $eventFactory->seed(0, $options['event']);
        $events[] = $result['event'];
        $organizers[] = $result['organizer'];
        
        $teamMemberFactory = new TeamMemberFactory();
        $teamResult = $teamMemberFactory->seed();
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
                    'payment_status' => 'completed', 
                    'join_status' => 'confirmed',    
                    'vote_ongoing' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $joinEvents[] = $joinEvent;
            }
        }

        foreach ($joinEvents as $joinEvent) {
            foreach ($members as $teamMember) {
                if ($teamMember->team_id == $joinEvent->team_id) {
                    RosterMember::updateOrCreate([
                        'user_id' => $teamMember->user_id,
                        'join_events_id' => $joinEvent->id,
                        'team_member_id' => $teamMember->id,
                    ],[
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
                        if ($participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NOTMAL')) {
                            $transactionDetails = [
                                'name' => "Top up for event: RM {$event->eventName}",
                                'type' => "Top up: RM {$amount}",
                                'link' => null,
                                'summary' => "Wallet RM $amount",
                            ];
                        } else {
                            $transactionDetails = [
                                'name' => "Payment for {$event->eventName}",
                                'type' => "Top up for Event: RM $amount",
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => "FAKE, FAKE, FAKE",
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
                                        'teamId' => $teamMember->id
                                    ]
                                ]);
                            } catch (\Exception $e) {
                                $stripeIntent = $stripeConnection->createPaymentIntent([
                                    'amount' => $amount * 100,
                                    'currency' => 'myr',
                                    'capture_method' => $participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NOTMAL') ?
                                        'manual' : 'automatic',
                                    'metadata' => [
                                        'joinEventId' => $joinEvent->id,
                                        'memberId' => $joinEvent->teams_id,
                                        'teamId' => $teamMember->id
                                    ]
                                ]);
                            }
                        } else {
                            $stripeIntent = $stripeConnection->createPaymentIntent([
                                'amount' => $amount * 100,
                                'currency' => 'myr',
                                'capture_method' => $participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NOTMAL') ?
                                    'manual' : 'automatic',
                                'metadata' => [
                                    'joinEventId' => $joinEvent->id,
                                    'memberId' => $joinEvent->teams_id,
                                    'teamId' => $teamMember->id
                                ]
                            ]);
                        }

                        if ($recordStripe) {
                            $recordStripe->update([
                                'payment_id' => $stripeIntent['id'],
                                'payment_status' => $stripeIntent['status'],
                                'payment_amount' => $amount,
                                'updated_at' => now()
                            ]);
                        } else {
                            $recordStripe = RecordStripe::create([
                                'payment_id' => $stripeIntent['id'],
                                'payment_status' => $stripeIntent['status'],
                                'payment_amount' => $amount,
                                'created_at' => now()
                            ]);
                        }
                        
                        // Set transaction details based on register_time
                        if ($participantPaymentOption['register_time'] == config('constants.SIGNUP_STATUS.NOTMAL')) {
                            $transactionDetails = [
                                'name' => "Entry Fee Hold: RM {$event->eventName}",
                                'type' => 'Event Entry Fee Hold',
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => "User Wallet RM {$amount}",
                            ];
                        } else {
                            $transactionDetails = [
                                'name' => "Entry Fee: RM {$event->eventName}",
                                'type' => 'Event Entry Fee',
                                'link' => route('public.event.view', ['id' => $event->id]),
                                'summary' => "Fake, Fake, Fake",
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
                        'register_time' => null,
                        'history_id' => $transaction->id,
                        'type' => $participantPaymentOption['type']
                    ]);
                }
            }
        }
        
        return [
            'events' => $events,
            'joinEvents' => $joinEvents,
            'organizer' => $organizers,
            ...$teamResult
        ];
    }
}
