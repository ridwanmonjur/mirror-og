<?php

namespace App\Http\Livewire\Participant\Profile;

use Livewire\Component;
use App\Models\ActivityLogs;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ShowActivityLogs extends Component
{
    public $userId = null;

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
            ->where('subject_type', '\App\Models\User')
            ->paginate($perPage, ['*'], 'page', $this->page);
        
        $this->hasMore = $activityLogs->hasMorePages();
        $this->totalItems = $activityLogs->items();
        $this->loadedItems += $perPage; 
    }

    public function loadMore()
    {
        $perPage = 10; 
        $this->page++;
        $newItems = ActivityLogs::where('subject_id', $this->userId)
            ->where('subject_type', '\App\Models\User')
            ->paginate($perPage, ['*'], 'page', $this->page)
            ->items();
        
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
