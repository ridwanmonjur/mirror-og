<div x-data="profileStatsData" class="mt-4">
    <!-- Tabs -->
    <template x-if="role !== 'ORGANIZER'">
        <div class="">
            <button class="btn btn-link ps-0 me-4" x-on:click="openModal('followers')">
                <span x-text="count['followers']"> </span> Followers
            </button>
            <button class="btn btn-link ps-0 me-4" x-on:click="openModal('following')">
                <span x-text="count['following']"> </span>
                Following
            </button>
            <button class="btn btn-link ps-0 me-4" x-on:click="openModal('friends')">
                <span x-text="count['friends']"> </span>
                Friends
            </button>
        </div>
    </template>
    <template x-if="role === 'ORGANIZER'">
        <div>
            <button class="btn btn-link">
                Followers
            </button>
        </div>
    </template>

    <!-- Modals -->
    <div id="connectionModal" class="modal fade" tabindex="-1" aria-labelledby="connectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mx-3">
                <div class="modal-header my-3">
                    <h5 class="ms-3" x-text="currentTab?.toUpperCase()">
                    </h5>
                    <button type="button" class="btn-close me-3" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body mx-3 mb-3">
                    <!-- Profile Info -->
                    <div class="table-responsive">
                        <template x-if="!(currentTab in connections) || !connections[currentTab][0]">
                            <div class="text-center my-4">No users in this list.</div>
                        </template>
                        <template x-if="currentTab in connections && connections[currentTab][0]">
                            <div>
                                <table class="member-table table responsive table-striped mb-0">
                                    <tbody>
                                        <template x-for="user in connections[currentTab]" :key="user.id">
                                            <tr>
                                                <td class="text-center px-3 py-3" style="width: 50px;">
                                                    <a :href="`/view/${user?.role?.toLowerCase()}/${user.id}` ">
                                                        <svg class="cursor-pointer"
                                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                            <path
                                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td class="py-3 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="'/storage/' + user.userBanner"
                                                            class="rounded-circle me-3" width="40" height="40"
                                                            onerror="this.src='/assets/images/404.png';"
                                                        >
                                                        <span x-text="user.name"></span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-3" x-text="user.email"></td>
                                                <td clas="py-3 px-3" x-text="user.name"></td>
                                                <td class="text-end py-3 px-3" x-text="formatDate(user.created_at)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <!-- Pagination -->
                        <template x-if="next_page[currentTab]">
                            <div aria-label="Page navigation"  class="mt-3 d-flex justify-content-center">
                                <button class="btn  btn-link " x-on:click="loadNextPage">Next page</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
