<div class="row">
    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
        <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
            <div class="ps-5 ps-5 text-start">
                <h5 class="text-start my-3"> Counter Explanation (Optional) </h5>
                <p class="my-0"> Responding Team </p>
                <img v-bind:src="'/storage/' + report.teams[dispute[reportUI.matchNumber].response_teamNumber]?.banner"
                    alt="Team Banner" width="50" height="50"
                    onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                    class="mb-1 border border-2 popover-content-img rounded-circle object-fit-cover">
                <p class="text-primary">
                    <span v-text="report.teams[dispute[reportUI.matchNumber].response_teamNumber].name">
                    </span>
                    <span v-show="reportUI.teamNumber == dispute[reportUI.matchNumber].response_teamNumber">(Your
                        Team)
                    </span>
                </p>

                <p class="my-0" v-html="dispute[reportUI.matchNumber].response_explanation">
                </p>
                <p class="text-primary" style="white-space: pre-wrap;"
                    v-html="dispute[reportUI.matchNumber].dispute_description">
                </p>
                <div class="mb-2">
                    <template v-if="dispute[reportUI.matchNumber]?.response_image_videos[0]">
                        <div>
                            <p class="my-0">Image/ Video Evidence: <span class="text-red">*<span></p>

                            <template v-for="imgVideo in dispute[reportUI.matchNumber].response_image_videos"
                                :key="imgVideo">
                                <div>
                                    <template v-if="imgVideo.startsWith('media/img')">
                                        <img v-bind:src="'/storage/' + imgVideo"
                                            class="object-fit-cover border border-primary"
                                            v-on:click="showImageModal(imgVideo, 'image')" height="100px" width="100px" />
                                    </template>

                                     <template v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            class="me-3"
                                            v-on:click="showImageModal(imgVideo, 'video')"
                                            width="60"
                                            height="60"
                                        >
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" fill="#666666"/>
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>
