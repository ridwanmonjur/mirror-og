@php
    use Carbon\Carbon;
@endphp
<div @class(["chatbox-container",
    'd-none' => $isChatClosed || $isChatNotInited
  ])>
    <div>
        <div class="row container d-flex justify-content-center">
            <div>

                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Chat Messages
                            @if ($messageCount)
                              <span data-bs-toggle="tooltip" title="{{$messageCount . ' New Messages'}}" class="badge bg-primary">
                                  {{$messageCount}}
                              </span>
                            @endif
                        </h3>
                        <div class="box-tools float-end">
                            <button type="button" class="btn btn-tool p-0" data-bs-toggle="collapse">
                                {{-- minimise --}}
                                <svg xmlns="http://www.w3.org/2000/svg"
                                  wire:click="toggleChatVisibility()"
                                  width="30" height="30"
                                    fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8" />
                                </svg>
                            </button>

                            <button type="button" class="btn btn-tool p-0" data-widget="remove">
                                {{-- close --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                    fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"
                                    wire:click="closeChatAndLive()"  
                                  >
                                    <path
                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="direct-chat-messages">
                            @foreach ($conversations as $conversation)
                                @livewire('chat.in-page-message-list', [
                                    'messages' => $conversation->messages,
                                    'user' => $user,
                                    'userProfile' => $userProfile,
                                    'conversationId' => $conversation->id
                                ], key("conversation-" . $loop->index . $conversation->id ))
                            @endforeach
                        </div>
                    </div>

                    <div class="box-footer">
                       <form wire:submit.prevent="sendMessage">
                            <div class="input-group">
                                <input type="text" name="currentMessage" wire:model="currentMessage" placeholder="Type Message ..." class="form-control" wire:model="message">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary text-light btn-flat">Send</button>
                                </span>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>
