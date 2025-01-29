
<div 
    {{-- x-data="{{ $duration . 'Activities'}}"  --}}
    v-scope="ActivityLogs(
        {{$userProfile->id}},
        '{{$duration}}'
    )"
    @vue:mounted="init"
    class="activity-logs"
>
    <template v-if="items && items[0]">
        <div class="mt-2">
            <template v-for="item in items" :key="item.id">
                <div class="tab-size mb-3 mx-auto">
                    <span class="me-1" v-html="item.log"></span>
                    <span style="color: #565656;" 
                          v-text="formatDate(item.created_at)">
                    </span>
                </div>
            </template>
        </div>
    </template>

    <template v-if="items && !items[0]">
        <div class="tab-size mt-2">
            No <span v-text="duration"></span> activities
        </div>
    </template>

    <template v-if="hasMore">
        <div class="text-center mt-2">
            <button v-on:click="loadMore" 
                    class="btn btn-link btn-sm text-primary">
                Load More
            </button>
        </div>
    </template>
    {{-- <script src="{{ asset('/assets/js/participant/ActivityLogs.js') }}"></script> --}}
    <br>
</div>