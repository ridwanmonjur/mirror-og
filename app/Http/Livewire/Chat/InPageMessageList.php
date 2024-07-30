<?php

namespace App\Http\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\Log;


class InPageMessageList extends Component
{
    public  \Illuminate\Database\Eloquent\Collection $messages;
    public  $user;
    public $userProfile;
    public $conversationId;

    protected $listeners = ['addMessage' => 'addMessage'];

    public function addMessage($newMessage) {
        Log::info('message()>>>');
        Log::info($newMessage);
        Log::info($this->messages instanceof \Illuminate\Database\Eloquent\Collection);

        Log::info($this->conversationId);
        if ($newMessage['conversationId'] == $this->conversationId) {
            $newOne = new Message($newMessage['newMessage']);
            $newOne->id = $newMessage['newMessage']['id'];
            $this->messages->push( $newOne);
            Log::info("newONe()");
            Log::info($newOne);
            Log::info($this->messages);

        }
    }

    
    public function render()
    {
        return view('livewire.chat.in-page-message-list');
    }
}
