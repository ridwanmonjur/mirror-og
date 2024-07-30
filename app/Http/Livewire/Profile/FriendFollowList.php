<?php

namespace App\Http\Livewire\Profile;

use App\Models\Friend;
use App\Models\Organizer;
use App\Models\OrganizerFollow;
use App\Models\ParticipantFollow;
use Livewire\Component;
use Livewire\WithPagination;

class FriendFollowList extends Component
{
    use WithPagination;
    public $userId = 0;
    public $currentTab = 'Follower';
    public $name = 0;
    public $page = 1;
    public $propertyName = 'followeeUser';

    public function initData()
    {
        $propertyName = null;
        if ($this->name == 'Follow') {
            $propertyName = 'followeeUser';
            $data = ParticipantFollow::where('participant_followee', $this->userId)
                ->with($propertyName)
                ->paginate($this->page, ['*'], $propertyName);
            
            $this->propertyName = $propertyName;
            return $data;
        } elseif ($this->name == 'Following') {
            $propertyName = 'followerUser';
            $data = ParticipantFollow::where('participant_follower', $this->userId)
                ->with($propertyName)
                ->paginate($this->page, ['*'], $propertyName);
            
            $this->propertyName = $propertyName;
            return $data;
        } elseif ($this->name == 'OrgFollow') {
            $propertyName = 'participantUser';
            $data = OrganizerFollow::where('participant_followee', $this->userId)
                ->with($propertyName)
                ->paginate($this->page, ['*'], $propertyName);
            
            $this->propertyName = $propertyName;
            return $data;
        } elseif ($this->name == 'Friends') {
            $propertyName = 'relatedUser';
            $data = Friend::where(function ($query) {
                $query->where('user1_id', $this->userId)->orWhere('user2_id', $this->userId);
            })
                ->where('status', 'accepted')
                ->with(['user1', 'user2'])  
                ->paginate($this->page, ['*'], $propertyName);
    
            $data->getCollection()->transform(function ($friend) {
                $friend->relatedUser = $friend->user1_id != $this->userId ? $friend->user1 : $friend->user2;
                return $friend;
            });

            $this->propertyName = $propertyName;
            return $data;
        }

    }

    public function render()
    {
        return view('livewire.profile.friend-follow-list', [
            'data' => $this->initData(),
            'currentTab' => $this->currentTab
        ]);
    }
}
