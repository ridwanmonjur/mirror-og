<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Console\Traits\RespondTaksTrait;
use App\Jobs\HandleEventJoinConfirm;
use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\StripeConnection;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RespondTasks extends Command
{
    use PrinterLoggerTrait, RespondTaksTrait;

    protected $stripeService;
    protected $taskIdParent;

    public function __construct(StripeConnection $stripeService)
    {
        parent::__construct();
        $now = Carbon::now();
        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        $this->taskIdParent = $taskId;
        $this->initializeTrait($stripeService, $taskId);
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'tasks:respond {type=0 : The task type to process: 1=started, 2=live, 3=ended, 4=reg_over, 0=all} {--event_id= : Optional event ID to filter tasks}';

    protected $description = 'Respond tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $type = (int) $this->argument('type');
        $eventId = $this->option('event_id');
        $eventIdInt = 0;
        Log::info("Event of type {$type} ran");
        $taskId = $this->taskIdParent;

        try {
            $endedTaskIds = [];
            $liveTaskIds = [];
            $startedTaskIds = [];
            $regOverTaskIds = [];

            if ($type === 0) {
                $tasks = Task::where('taskable_type', "EventDetail")
                    ->where('action_time', '>=', $now->copy()->subMinutes(5))
                    ->where('action_time', '<=', $now->copy()->addMinutes(29))
                    ->get();
            } else {
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)->where('taskable_type', "EventDetail")->get();
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
                        $endedTaskIds[] = $task->taskable_id;
                        break;
                    case 'live':
                        $liveTaskIds[] = $task->taskable_id;
                        break;
                    case 'started':
                        $startedTaskIds[] = $task->taskable_id;
                        break;
                    case 'reg_over':
                        $regOverTaskIds[] = $task->taskable_id;
                        break;
                }
            }

            // dd(vars: $tasks);

            $with = ['roster:id,team_id,user_id,join_events_id', 'members.user:id,name', 'eventDetails.user:id,name', 'eventDetails.tier:id,eventTier'];

            if ($type == 0 || $type == 1) {
                $withNew = [...$with, 'payments.history', 'payments.transaction'];

                $shouldCancel = false;

                foreach ($startedTaskIds as $eventId) {
                    try {
                        $event = EventDetail::where('id', $eventId)
                            ->with('tier')
                            ->withCount([
                                'joinEvents' => function ($q) {
                                    $q->where('join_status', 'confirmed');
                                },
                            ])
                            ->first();
                        Log::info("Found {$event->eventName}");

                        if ($event) {
                            $shouldCancel = $this->checkAndCancelEvent($event);

                            if (!$shouldCancel) {
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
                foreach ($liveTaskIds as $eventId) {
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
                $totalEvents = count($endedTaskIds);
                $processedEvents = 0;
                $failedEvents = 0;

                foreach ($endedTaskIds as $eventId) {
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

                        if (!$event) {
                            throw new Exception('Event not found after status update');
                        }

                        [$playerNotif, $orgNotif, $logs, $memberIdList, $prizeDetails] = $this->getEndedNotifications($endedEvents, $event->tier);

                        Log::info($playerNotif);
                        Log::info($orgNotif);
                        Log::info($logs);
                        Log::info($memberIdList);
                        Log::info($prizeDetails);
                        $this->handleEventTypes([$playerNotif, $orgNotif], $endedEvents);

                        $this->handlePrizeAndActivityLogs($logs, $endedEvents, $memberIdList, $prizeDetails);

                        $processedEvents++;

                        Log::info('Event processing completed', [
                            'event_id' => $eventId,
                            'event_name' => $event->eventName ?? 'Unknown',
                            'participants_count' => $endedEvents->count(),
                            'has_prizes' => !empty($prizeDetails),
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

                        // Optionally try to revert event status if it was updated but processing failed
                        try {
                            EventDetail::where('id', $eventId)->update(['status' => 'ACTIVE']);
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

                // Final summary log
                Log::info('Event processing batch completed', [
                    'total_events' => $totalEvents,
                    'processed_successfully' => $processedEvents,
                    'failed_events' => $failedEvents,
                    'success_rate' => $totalEvents > 0 ? round(($processedEvents / $totalEvents) * 100, 2) . '%' : '0%',
                ]);
            }

            if ($type == 0 || $type == 4) {
                foreach ($regOverTaskIds as $regOverTaskId) {
                    try {
                        $event = EventDetail::where('id', $regOverTaskId)
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
