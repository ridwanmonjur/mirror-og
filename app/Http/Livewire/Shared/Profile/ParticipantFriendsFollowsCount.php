<?php

namespace App\Http\Livewire\Shared\Profile;

use App\Models\Friend;
use App\Models\ParticipantFollow;
use Livewire\Component;

class ParticipantFriendsFollowsCount extends Component
{
    public $count = 0;

    public $name = 0;

    public $isClicked = 0;

    public $hyperlink = '';

    public $userId = 0;

    public $text = '';

    public function render()
    {
        return view('livewire.shared.profile.participant-friends-follows-count');
    }

    public function mount()
    {
        if ($this->userId) {
            if ($this->name === 'follower') {
                $this->count = ParticipantFollow::where('participant_followee', $this->userId)
                    ->count();
                $this->hyperlink = route('user.stats', [
                    'id' => $this->userId, 'type' => 'Follow',
                ]);
                $this->text = 'Follower'.bladePluralPrefix($this->count);
            } elseif ($this->name === 'followee') {
                $this->count = ParticipantFollow::where('participant_follower', $this->userId)
                    ->count();
                $this->hyperlink = route('user.stats', [
                    'id' => $this->userId, 'type' => 'Following',
                ]);
                $this->text = 'Following';
            } elseif ($this->name === 'friend') {
                $this->count = Friend::where(function ($query) {
                    $query->where('user1_id', $this->userId)
                        ->orWhere('user2_id', $this->userId);
                })
                    ->where('status', 'accepted')
                    ->count();
                $this->hyperlink = route('user.stats', [
                    'id' => $this->userId, 'type' => 'Friends',
                ]);
                $this->text = 'Friend'.bladePluralPrefix($this->count);
            }
        }
    }
}
