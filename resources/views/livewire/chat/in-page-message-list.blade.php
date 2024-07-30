<div>
    @foreach ($messages as $msg)
        @php
            $isMyMessage = $user->id === $msg->user_id;
            if ($isMyMessage) {
                $classNameDirection = 'left';
                $banner = $user->userBanner;
                $name = $user->name;
                $hyperlink = route('public.' . strtolower($user->role) . '.view', ['id' => $user->id]);
            } else {
                $classNameDirection = 'right';
                $banner = $userProfile->userBanner;
                $name = $userProfile->name;
                $hyperlink = route('public.' . strtolower($userProfile->role) . '.view', ['id' => $userProfile->id]);
            }

            $classNamePull = 'pull-' . $classNameDirection;
        @endphp
        <div wire:key="message-{{ $msg->id }}" class="direct-chat-msg {{ $classNameDirection }}">
            <div class="direct-chat-info clearfix">
                <a href="{{ $hyperlink }}">
                    <span class="direct-chat-name {{ $classNamePull }}">{{ $name }}</span>
                    <span
                        class="direct-chat-timestamp {{ $classNamePull }}">{{ \Carbon\Carbon::parse($msg->created_at)->isoFormat('Do MMMM YYYY') }}</span>
                </a>
            </div>
            <a href="{{ $hyperlink }}">
                <img class="direct-chat-img" src="{{ '/storage/' . $banner }}" alt="message user image">
            </a>
            <div class="direct-chat-text">
                {{ $msg->text }}
            </div>
        </div>
    @endforeach
</div>
