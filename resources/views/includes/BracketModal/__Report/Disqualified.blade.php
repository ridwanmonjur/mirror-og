<div class="py-0 my-0"> 
    <div class="py-0 my-0">
        <svg width="50" height="50" class="border border-dark border-2 rounded-circle py-1 my-2" viewBox="0 0 50 50"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M35.7,14.3c0.4,0.4,0.4,1,0,1.4L26.4,25l9.3,9.3c0.4,0.4,0.4,1,0,1.4c-0.4,0.4-1,0.4-1.4,0L25,26.4l-9.3,9.3
                c-0.4,0.4-1,0.4-1.4,0c-0.4-0.4-0.4-1,0-1.4l9.3-9.3l-9.3-9.3c-0.4-0.4-0.4-1,0-1.4c0.4-0.4,1-0.4,1.4,0l9.3,9.3l9.3-9.3
                C34.7,13.9,35.3,13.9,35.7,14.3z" fill="currentColor" />
        </svg>
        <div>
            <span v-if="reportUI.matchNumber == 0">
                No winner for Game <span v-text="reportUI.matchNumber+1"></span>
            </span>
            <span v-else>
                Both teams have been disqualified.
            </span>
        </div>
    </div>
</div>
