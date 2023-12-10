<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class EventDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tier()
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }

    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function game()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function statusResolved()
    {
        if ($this->status == "DRAFT" || $this->status == "PREVIEW") {
            return $this->status;
        }
        $carbonPublishedDateTime = $this->createCarbonDateTimeFromDB($this->sub_action_public_date, $this->sub_action_public_time);
        $carbonEndDateTime = $this->createCarbonDateTimeFromDB($this->endDate, $this->endTime);
        $carbonStartDateTime = $this->createCarbonDateTimeFromDB($this->startDate, $this->startTime);
        if (!$carbonEndDateTime || !$carbonStartDateTime) {
            Log::error("EventDetail.php: statusResolved: EventDetail with id= " . $this->id
                . " and name= " . $this->eventName . " has null end or start date time");
            return "ERROR";
        }
        $carbonNow = Carbon::now()->utc();
        if ($carbonEndDateTime > $carbonNow) {
            return "ENDED";
        } else {
            if ($carbonPublishedDateTime && $carbonPublishedDateTime < $carbonNow) {
                return "SCHEDULED";
            } elseif ($carbonStartDateTime < $carbonNow) {
                return "UPCOMING";
            }
            else return "ONGOING";
        }
    }
    public function fixTimeToRemoveSeconds($time)
    {
        if ($time == null) {
            return null;
        }
        if (substr_count($time, ':') === 2) {
            $time = explode(':', $time);
            $time = $time[0] . ':' . $time[1];
        }
        return $time;
    }

    public function createCarbonDateTimeFromDB($date, $time)
    {
        if ($date == null || $time == null) {
            return null;
        }
        $carbonDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $this->fixTimeToRemoveSeconds($time))
            ->utc();
        return $carbonDateTime;
    }
}
