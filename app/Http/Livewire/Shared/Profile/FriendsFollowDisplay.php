<?php

namespace App\Http\Livewire\Shared\Profile;

use App\Models\User;
use Livewire\Component;

class FriendsFollowDisplay extends Component
{
    public $currentTab = 'Follower';

    public $userProfile = null;

    public $userId;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if ($this->userId) {
            $this->userProfile = User::where('id', $this->userId)
                ->first();
        }
    }

    public function setTab($currentTab)
    {
        $this->currentTab = $currentTab;
    }

    public function render()
    {
        return view('livewire.shared.profile.friends-follow-display');
    }
}
