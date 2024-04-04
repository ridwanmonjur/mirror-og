 <div class="border-dark border-2 border-bottom text-center px-2">
    <div class="d-flex justify-content-between align-items-center py-3">
        <div style="height: 45px; width: 80px;"
            class="bg-dark d-flex justify-content-center align-items-center text-light rounded-circle">
            {{ $notification['icon'] }}
        </div>
        <div style="text-overflow: ellipsis; overflow: hidden;" class="text-start ms-2">
            <small> {{ $notification['message'] }}</small>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center py-3">
        <a href="{{$notification['url']}}" class="btn btn-link">Go to link</a>
        <div>
    </div>
</div>