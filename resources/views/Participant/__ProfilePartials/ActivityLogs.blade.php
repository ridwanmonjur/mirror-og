
<div x-data="{{ $duration . 'Activities'}}" 
    class="activity-logs"
>
    <template x-if="items && items[0]">
        <div class="mt-2">
            <template x-for="item in items" :key="item.id">
                <div class="tab-size mb-3 mx-auto">
                    <span class="me-1" x-html="item.log"></span>
                    <span style="color: #565656;" 
                          x-text="formatDate(item.created_at)">
                    </span>
                </div>
            </template>
        </div>
    </template>

    <template x-if="items && !items[0]">
        <div class="tab-size mt-2">
            No <span x-text="duration"></span> activities
        </div>
    </template>

    <template x-if="hasMore">
        <div class="text-center mt-2">
            <button x-on:click="loadMore" 
                    class="btn btn-link btn-sm text-primary">
                Load More
            </button>
        </div>
    </template>
    <script src="{{ asset('/assets/js/participant/ActivityLogs.js') }}"></script>
    <br>
</div>