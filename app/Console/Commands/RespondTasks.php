<?php

namespace App\Console\Commands;

use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use App\Models\Task;
use App\Traits\TasksTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class RespondTasks extends Command
{
    use TasksTrait;

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
    protected $signature = 'tasks:respond';

    protected $description = 'Respond tasks in the database';


    /**
     * Execute the console command.
     */
    public function handle(){
        $this->getTodayTasksByName();
    }

    public function getTodayTasksByName()
    {
        $today = Carbon::now();
        $commandName = 'tasks:create';
        $id = $this->logEntry($today, $commandName);
        try {
            $now = Carbon::now();
            $today = Carbon::today();

            $tasks = Task::whereDate('action_time', Carbon::today())->where('action_time', '>=', Carbon::now())->where('action_time', '<=', Carbon::now()->addMinutes(30))->orderBy('action')->get()->groupBy('task_name');

            $eventIdList = $tasks['ended']->pluck('event_id')->toArray();

            $paymentData = DB::table('event_details')
                ->join('join_events', 'event_details.id', '=', 'join_events.event_details_id')
                ->join('participant_payments', 'join_events.id', '=', 'participant_payments.join_events_id')
                ->join('all_payment_transactions',  'all_payment_transactions.id', '=', 'participant_payments.payment_id')
                ->whereIn('event_details.id', $eventIdList)
                ->where('all_payment_transactions.payment_status', 'requires_capture')
                ->select('all_payment_transactions.payment_id', 'all_payment_transactions.id')
                ->get();
        
            $resultList = $paymentData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'payment_id' => $item->payment_id
                ];
            })->toArray();

            $updatedPayments = [];
            foreach ($resultList as $item) {
                try {
                    $stripe = new StripePayment();
                    $paymentIntent = $stripe->retrieveStripePaymentByPaymentId($item['payment_id']);

                    if ($paymentIntent->status === 'requires_capture') {
                        $capturedPayment = $paymentIntent->capture();

                        $updatedPayments[] = [
                            'id' => $item['id'],
                            'payment_id' => $item['payment_id'],
                            'payment_status' => $capturedPayment['status']
                        ];
                    }
                } catch (Exception $e) {
                    $errorMsg = "Payment Intent: {$item['payment_id']} error: " . $e->getMessage();
                    $this->logError($id, $errorMsg);
                }
            }


            if (!empty($updatedPayments)) {
                DB::beginTransaction();
                try {
                    foreach ($updatedPayments as $payment) {
                        PaymentTransaction::where('id', $payment['id'])
                            ->update(['payment_status' => $payment['payment_status']]);
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->logError($id, "Bulk update failed: " . $e->getMessage());
                }
            }

            $now = Carbon::now();
            $this->logExit($id, $now);
            return $tasks;
        } catch (Exception $e) {
            $this->logError($id, $e->getMessage());
        }
    }
}
