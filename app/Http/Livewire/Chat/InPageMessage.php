<?php

namespace App\Http\Livewire\Chat;

use App\Events\Chat;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class InPageMessage extends Component
{
    public $user;
    public $userProfile;
    public $conversations = [];
    public $messageCount = 0;
    public $isChatNotInited = true;
    public $isChatClosed = true;
    public $currentMessage = '';
    public $lastConversation = null;
    public $conversationIndex = 0;
    public $receivedUnreadMessages = [];

    protected $listeners = ['chatStarted' => 'fetchMessages'];

    public function toggleChatVisibility()
    {
        $this->isChatClosed = !$this->isChatClosed;
    }

    public function closeChatAndLive()
    {
        $this->isChatClosed = true;
    }

    public function fetchMessages()
    {
        if (!$this->isChatNotInited) {
            $this->isChatClosed = false;
            return;
        }

        $this->isChatNotInited = false;
        $this->isChatClosed = false;
        $this->messageCount = 20;
        $user = $this->user;
        $userProfile = $this->userProfile;

        $this->conversations = Conversation::where(function ($query) use ($user, $userProfile) {
            $query->where('user1_id', $user->id)->where('user2_id', $userProfile->id);
        })
            ->orWhere(function ($query) use ($user, $userProfile) {
                $query->where('user1_id', $userProfile->id)->where('user2_id', $user->id);
            })
            ->with('messages')
            ->get();

        $this->lastConversation = $this->conversations->last()->id ?? null;
    }

    public function sendMessage()
    {
        $this->validate([
            'currentMessage' => 'required|string',
        ]);

        if (is_null($this->lastConversation)) {
            $conversation = Conversation::create([
                'user1_id' => $this->user->id,
                'user2_id' => $this->userProfile->id,
                'initiator_id' => $this->user->id,
            ]);
            $message = Message::create([
                'user_id' => $this->user->id,
                'conversation_id' => $conversation->id,
                'text' => $this->currentMessage,
                'read_at' => now(),
            ]);
            // $conversation->messages[] = $message;
            $this->conversations[] = $conversation;
            $this->lastConversation = $conversation->id;
            // $this->lastConversation = $conversation->id;
        } else {
            $message = Message::create([
                'user_id' => $this->user->id,
                'conversation_id' => $this->lastConversation,
                'text' => $this->currentMessage,
                'read_at' => now(),
            ]);

            $this->emitTo('chat.in-page-message-list', 'addMessage', [
                'conversationId' => $this->lastConversation,
                'newMessage' => $message
            ]);

        }
        $this->currentMessage = '';
        event(new Chat($this->userProfile->id, $message, $this->receivedUnreadMessages, $this->user));
    }

    public function render()
    {
        return view('livewire.chat.in-page-message');
    }
}
