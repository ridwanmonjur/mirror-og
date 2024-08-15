<?php

namespace App\Http\Livewire\Participant\Profile;

use App\Models\ActivityLogs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ShowActivityLogs extends Component
{
    public $userId = null;

    public $duration = 'new';

    public $page = 1;

    public $totalItems = [];

    public $loadedItems = 0;

    public $hasMore = true;

    public function mount()
    {
        $this->loadActivityLogs();
    }

    public function loadActivityLogs()
    {
        $perPage = 1;
        $activityLogsQuery = ActivityLogs::where('subject_id', $this->userId)
            ->where('subject_type', User::class);
            Log::info("==========> Duration: {$this->duration} Page: {$this->page}");
        if ($this->duration === 'new') {
            $activityLogsQuery->whereDate('created_at', Carbon::today());
        } elseif ($this->duration === 'recent') {
            $activityLogsQuery->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::today()]);
        } else {
            $activityLogsQuery->whereDate('created_at', '<', Carbon::now()->subWeek()->startOfWeek());
        }

        $activityLogs = $activityLogsQuery->paginate($perPage, ['*'], 'page', $this->page);
        Log::info("===========> Activity Logs:" . $activityLogs->hasMorePages() . "\n");

        $this->hasMore = $activityLogs->hasMorePages();
        $this->totalItems = array_merge($this->totalItems, $activityLogs->items());
        $this->loadedItems += $perPage;
        $this->page+=1;
    }

    

    public function render()
    {
        return view('livewire.participant.profile.show-activity-logs');
    }
}
