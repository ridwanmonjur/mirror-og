<span class="d-inline p-0 m-0" style="display: inline !important;">
    <span class="cursor-pointer d-inline ps-0" x-on:click="openModal">
        <span data-count="{{ $followersCount }}"> {{ $followersCount }}
            follower{{ bladePluralPrefix($followersCount) }}
        </span>
    </span>
</span>
<!-- Modals -->
<div id="connectionModal" class="modal fade" tabindex="-1" aria-labelledby="connectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="top: 10vh; color: black;">
        <div class="modal-content mx-3">
            <div class="modal-body px-3 mx-3 mb-3">
                <div class="d-flex justify-content-between">
                    <h5 class="ms-3 my-3">
                        Followers
                    </h5>
                    <button type="button" class="btn-close me-3 my-3" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <!-- Profile Info -->
                <div >
                    <template x-if="!connections[0]">
                        <div class="text-center my-4">No users in this list.</div>
                    </template>
                    <template x-if="connections[0]">
                        <div class="table-responsive table">
                            <table style="font-size: normal !important;" class="member-table responsive table-striped mb-0 table-sm">
                                <tbody>
                                    <template x-for="user in connections" :key="user.id">
                                        <tr>
                                            <td class="text-center py-1 px-2" style="width: 50px;">
                                                <a :href="`/view/${user?.role?.toLowerCase()}/${user.id}`">
                                                    <svg class="cursor-pointer" xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" fill="currentColor"
                                                        viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path
                                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                </a>
                                            </td>
                                            <td class="py-1 px-2">
                                                <div class="d-flex align-items-center">
                                                    <img :src="'/storage/' + user.userBanner"
                                                        class="rounded-circle me-3" width="40" height="40"
                                                        onerror="this.src='/assets/images/404.png';">
                                                    <span x-text="user.name"></span>
                                                </div>
                                            </td>
                                            <td class="py-1 px-2" x-text="user.email"></td>
                                            <td class="py-1 px-2" x-text="formatDate(user.created_at)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <!-- Pagination -->
                    <template x-if="next_page">
                        <div aria-label="Page navigation" x-cloak x-show="next_page"
                            class="mt-3 d-flex justify-content-center"
                        >
                            <button class="btn  btn-link " x-on:click="loadNextPage">Next page</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
