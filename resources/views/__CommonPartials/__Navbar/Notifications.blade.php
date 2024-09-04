@foreach ($notificationList as $notification)
    <div data-loop-count="{{ $loop->index }}" @class([
        'text-center px-2 border-light border-0 notification-container',
        'notification-container-not-read' => is_null($notification->read_at),
        'notification-container-read' => !is_null($notification->read_at),
    ])>
        <div class="d-flex justify-content-start align-items-center pt-2">
            @if (isset($notification->data['image']))
                <img src="{{ '/storage' . '/' . $notification->data['image'] }}" alt= "Notification" width="45"
                    height="45" onerror="this.onerror=null; this.src='/assets/404.png';"
                    class="object-fit-cover rounded-circle">
            @else
                <div style="display: inline-block; height: 45px; min-width: 45px; max-width: 45px;"
                    class="d-flex justify-content-center align-items-center text-light rounded-circle random-bg-circle">
                    {{ $notification->type[0] }}
                </div>
            @endif
            <span style="text-overflow: ellipsis; overflow: hidden; word-wrap: break-word; font-size: 0.9375rem;"
                class="text-start ms-2 my-1">
                {!! $notification->data['data'] !!}
            </span>
        </div>
        <div class="d-flex justify-content-around align-items-center mt-1 pb-2">
            <a role="button" href="{{ $notification->data['links'][0]['url'] }}"
                class="border-bottom-primary border-bottom btn btn-link btn-sm">
                {{ $notification->data['links'][0]['name'] }}
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                </svg>
            </a>
            @if ($notification->read_at)
                <button role="button" class="btn btn-link" style="cursor-events: none;">
                    <span> Read </span>
                </button>
            @else
                <a role="button" class="border-bottom-primary border-bottom btn btn-link btn-sm mark-read"
                    onclick="setNotificationReadById(event, '{{ $notification->id }}', {{ $loop->index }})">

                    <span>Mark Read</span>
                </a>
            @endif
        </div>
    </div>
@endforeach

