<?php

namespace App\Http\Livewire\Participant\Profile;

use Livewire\Component;
use App\Models\ActivityLogs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
        $perPage = 10; 
        $activityLogs = ActivityLogs::where('subject_id', $this->userId)
            ->where('subject_type', '\App\Models\User');
        if ($this->duration == 'new') {
            $activityLogs->whereDate('created_at', Carbon::today());
        } elseif ($this->duration == 'recent') {
            $activityLogs->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::today()]);
        } elseif ($this->duration == 'older') {
            $activityLogs->where('created_at', '<', Carbon::now()->subWeek()->startOfWeek());
        }
    
        $activityLogs = $activityLogs->paginate($perPage, ['*'], 'page', $this->page);
        $this->hasMore = $activityLogs->hasMorePages();
        $this->totalItems = $activityLogs->items();
        $this->loadedItems += $perPage; 
    }

    public function loadMore()
    {
        $perPage = 10; 
        $this->page++;
        $newItemsQuery = ActivityLogs::where('subject_id', $this->userId)
            ->where('subject_type', User::class);
        if ($this->duration == 'new') {
            $newItemsQuery->whereDate('created_at', Carbon::today());
        } elseif ($this->duration == 'recent') {
            $newItemsQuery->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::today()]);
        } elseif ($this->duration == 'older') {
            $newItemsQuery->where('created_at', '<', Carbon::now()->subWeek()->startOfWeek());
        }

        $newItems = $newItemsQuery->paginate($perPage, ['*'], 'page', $this->page)->items();
        $this->totalItems = [
            ...$this->totalItems,
            ...$newItems
        ];
            
        $this->loadedItems += $perPage; 
    }

   
    public function render()
    {
        return view('livewire.participant.profile.show-activity-logs');
    }
}
