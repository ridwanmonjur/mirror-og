<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Livewire\Component;

class FriendsFollowDisplay extends Component
{
    public $currentTab = 'Follower';
    public $userProfile = null;
    public $userId = 0;

    protected $paginationTheme = 'bootstrap';

    public function mount() {
        if ($this->userId) {
            $this->userProfile = User::where('id', $this->userId)
                ->first();
        }
    }

    public function setTab($currentTab) {
        $this->currentTab = $currentTab;
    }

    public function render()
    {
        return view('livewire.profile.friends-follow-display');
    }
}
