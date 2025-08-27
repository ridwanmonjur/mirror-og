<?php

namespace App\Services;

use App\Models\Task;
use App\Traits\PrinterLoggerTrait;
use App\Traits\RespondTaksTrait;
use App\Jobs\HandleEventJoinConfirm;
use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\StripeConnection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RespondTaskService
{
    use PrinterLoggerTrait, RespondTaksTrait;

    protected $stripeService;

    protected $taskIdParent;

    public function __construct(StripeConnection $stripeService)
    {
        $this->stripeService = $stripeService;
        $now = Carbon::now();
        $taskId = $this->logEntry('Respond tasks in the database', 'tasks:respond {type=0 : The task type to process: 1=started, 2=live, 3=ended, 4=reg_over, 0=all} {--event_id= : Optional event ID to filter tasks}', '*/30 * * * *', $now);
        $this->taskIdParent = $taskId;
        $this->initializeTrait($stripeService, $taskId);
    }

    public function execute(int $type = 0, ?string $eventId = null, ?int $taskType = null): void
    {
        $now = Carbon::now();
        $eventIdInt = 0;
        Log::info("Event of type {$type} ran");
        $taskId = $this->taskIdParent;

        try {
            $endedEventIds = [];
            $liveEventIds = [];
            $startedEventIds = [];
            $regOverEventIds = [];

            if ($taskType !== null) {
                // Run for specific task type (from MiscController)
                // taskType 1=started, 2=live, 3=ended, 4=reg_over, 5=resetStart
                $taskNameMap = [
                    1 => 'started',
                    2 => 'live',
                    3 => 'ended', 
                    4 => 'reg_over',
                    5 => 'resetStart'
                ];
                
                $taskName = $taskNameMap[$taskType] ?? null;
                if ($taskName) {
                    if ($eventId !== null) {
                        $eventIdInt = (int) $eventId;
                        $tasks = Task::where('taskable_id', $eventIdInt)
                            ->where('taskable_type', 'EventDetail')
                            ->where('task_name', $taskName)
                            ->get();
                    } else {
                        $tasks = Task::where('taskable_type', 'EventDetail')
                            ->where('task_name', $taskName)
                            ->where('action_time', '>=', $now->copy()->subMinutes(5))
                            ->where('action_time', '<=', $now->copy()->addMinutes(29))
                            ->get();
                    }
                } else {
                    $tasks = collect(); // Empty collection for invalid taskType
                }
            } elseif ($type === 0) {
                if ($eventId !== null) {
                    // Run all task types for specific event
                    $eventIdInt = (int) $eventId;
                    $tasks = Task::where('taskable_id', $eventIdInt)->where('taskable_type', 'EventDetail')->get();
                } else {
                    // Run all tasks in time window
                    $tasks = Task::where('taskable_type', 'EventDetail')
                        ->where('action_time', '>=', $now->copy()->subMinutes(5))
                        ->where('action_time', '<=', $now->copy()->addMinutes(29))
                        ->get();
                }
            } else {
                // Run specific task type for specific event
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)->where('taskable_type', 'EventDetail')->get();
            }

            if ($type == 5) {
                try {
                    $event = EventDetail::where('id', $eventIdInt)->first();

                    if ($event) {
                        $event->status = null;
                        $event->status = $event->statusResolved();
                        $event->save();
                        $event->createRegistrationTask();
                        $event->createStatusUpdateTask();
                        $event->createDeadlinesTask();
                        Log::info("Reset event for ID: {$event->id}");
                    } else {
                        Log::error("No event to reset for ID: {$eventIdInt}");
                    }
                } catch (Exception $e) {
                    Log::info(message: "Failed to reset event for ID: {$eventIdInt}");

                    $this->logError($taskId, $e);
                }
            }

            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'ended':
                        $endedEventIds[$task->event_id] = true;
                        break;
                    case 'live':
                        $liveEventIds[$task->event_id] = true;
                        break;
                    case 'started':
                        $startedEventIds[$task->event_id] = true;
                        break;
                    case 'reg_over':
                        $regOverEventIds[$task->event_id] = true;
                        break;
                }
            }

            $endedEventIds = array_keys($endedEventIds);
            $liveEventIds = array_keys($liveEventIds);
            $startedEventIds = array_keys($startedEventIds);
            $regOverEventIds = array_keys($regOverEventIds);

            // dd(vars: $tasks);

            $with = ['roster:id,team_id,user_id,join_events_id', 'members.user:id,name', 'eventDetails.user:id,name', 'eventDetails.tier:id,eventTier'];

            if ($type == 0 || $type == 1) {
                $withNew = [...$with, 'payments.history', 'payments.transaction'];

                $shouldCancel = false;

                foreach ($startedEventIds as $eventId) {
                    try {
                        $event = EventDetail::where('id', $eventId)
                            ->with('tier')
                            ->withCount([
                                'joinEvents' => function ($q) {
                                    $q->where('join_status', 'confirmed');
                                },
                            ])
                            ->first();

                        if ($event) {
                            Log::info("Found {$event->eventName}");
                            $shouldCancel = $this->checkAndCancelEvent($event);

                            if (! $shouldCancel) {
                                $paidEvents = JoinEvent::where('event_details_id', $event->id)
                                    ->where('payment_status', 'completed')
                                    ->whereNot('join_status', 'confirmed')
                                    ->with(['eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id', 'payments.history', 'payments.transaction'])
                                    ->get();
                                $this->releaseFunds($event, $paidEvents);

                                $joinEvents = JoinEvent::where('event_details_id', $eventId)
                                    ->where('join_status', 'confirmed')
                                    ->with(['eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id', ...$withNew])
                                    ->get();

                                $this->capturePayments($event, $joinEvents);
                                $event->update(['status' => 'ONGOING']);
                                $this->handleEventTypes($this->getStartedNotifications($joinEvents), $joinEvents);
                            }
                        }
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 2) {
                foreach ($liveEventIds as $eventId) {
                    try {
                        $joinEvents = JoinEvent::where('event_details_id', $eventId)
                            ->where('join_status', 'confirmed')
                            ->with(['eventDetails:id,eventName,sub_action_public_date,sub_action_public_time,event_tier_id,user_id', ...$with])
                            ->get();
                        EventDetail::where('id', $eventId)->update(['status' => 'UPCOMING']);
                        $this->handleEventTypes($this->getLiveNotifications($joinEvents), $joinEvents);
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 3) {
                $totalEvents = count($endedEventIds);
                $processedEvents = 0;
                $failedEvents = 0;

                foreach ($endedEventIds as $eventId) {
                    try {
                        $endedEvents = JoinEvent::where('event_details_id', $eventId)
                            ->where('join_status', 'confirmed')
                            ->with(['eventDetails:id,eventName,endDate,endTime,event_tier_id,user_id', 'position', 'team:id,teamName,teamBanner', 'eventDetails.tier', ...$with])
                            ->get();

                        $statusUpdated = EventDetail::where('id', $eventId)->update(['status' => 'ENDED']);

                        if ($statusUpdated === 0) {
                            throw new Exception('Failed to update event status - event not found or already updated');
                        }

                        $event = EventDetail::where('id', $eventId)->with('tier')->first();

                        if (! $event) {
                            throw new Exception('Event not found after status update');
                        }

                        [$playerNotif, $orgNotif, $logs, $memberIdList, $prizeDetails] = $this->getEndedNotifications($endedEvents, $event->tier);

                        $this->handleEventTypes([$playerNotif, $orgNotif], $endedEvents);

                        $this->handlePrizeAndActivityLogs($logs, $endedEvents, $memberIdList, $prizeDetails);

                        $processedEvents++;

                        Log::info('Event processing completed', [
                            'event_id' => $eventId,
                            'event_name' => $event->eventName ?? 'Unknown',
                            'participants_count' => $endedEvents->count(),
                            'has_prizes' => ! empty($prizeDetails),
                        ]);
                    } catch (Exception $e) {
                        $failedEvents++;

                        Log::error('Event processing failed', [
                            'event_id' => $eventId,
                            'error' => $e->getMessage(),
                            'line' => $e->getLine(),
                            'file' => basename($e->getFile()),
                            'processed_so_far' => $processedEvents,
                            'failed_so_far' => $failedEvents,
                        ]);

                        try {
                            EventDetail::where('id', $eventId)->update(['status' => 'ONGOING']);
                            Log::info('Reverted event status due to processing failure', ['event_id' => $eventId]);
                        } catch (Exception $revertException) {
                            Log::error('Failed to revert event status', [
                                'event_id' => $eventId,
                                'revert_error' => $revertException->getMessage(),
                            ]);
                        }

                        // Continue processing other events
                        continue;
                    }
                }

                Log::info('Event processing batch completed', [
                    'total_events' => $totalEvents,
                    'processed_successfully' => $processedEvents,
                    'failed_events' => $failedEvents,
                    'success_rate' => $totalEvents > 0 ? round(($processedEvents / $totalEvents) * 100, 2).'%' : '0%',
                ]);
            }

            if ($type == 0 || $type == 4) {
                foreach ($regOverEventIds as $eventId) {
                    try {
                        $event = EventDetail::where('id', $eventId)
                            ->with('tier')
                            ->withCount([
                                'joinEvents' => function ($q) {
                                    $q->where('join_status', 'confirmed');
                                },
                            ])
                            ->first();

                        if ($event) {
                            $this->checkAndCancelEvent($event);
                        }
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
            Cache::clear();
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }

}