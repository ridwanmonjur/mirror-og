<div id="backgroundBanner" class="member-section px-2 pt-2">
    <div class="d-flex justify-content-end py-0 my-0 mb-2">
        <button data-bs-toggle="offcanvas" data-bs-target="#profileDrawer" v-cloak v-show="isEditMode"
            {{-- onclick="document.getElementById('backgroundInput').click();" --}} class="btn btn-secondary text-light rounded-pill py-2 me-3 fs-7">
            Change Background
        </button>
    </div>
    <br>
    <div v-cloak class="member-image">
        <div class="upload-container align-items-center">
            <label class="upload-label">
                <div v-cloak class="circle-container motion-logo">
                    <div class="uploaded-image"
                        style="background-image: url({{ '/storage' . '/' . $userProfile->userBanner }}  ); background-size: cover; 
                                        z-index: 99; background-repeat: no-repeat; background-position: center; {{ $frameStyles }}">
                    </div>
                    <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                        <a class="cursor-pointer" aria-hidden="true" data-fslightbox="lightbox"
                            data-href="{{ '/' . 'storage/' . $userProfile->userBanner }}">
                            <button class="btn btn-sm simple-button p-0 me-2"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                    <path
                                        d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                </svg>
                            </button>
                        </a>
                        @if ($isUserSame)
                            <button v-show="isEditMode" id="upload-button2" class="btn btn-sm simple-button p-0 z-99"
                                aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </label>
            @if ($isUserSame)
                <input type="file" id="image-upload" accept="image/*"
                    style="display: none;">
            @endif
        </div>
    </div>
    <div v-cloak class="member-details mx-auto text-center">
        <div v-show="isEditMode" class="pb-3">
            <div v-show="errorMessage != null" class="text-red" v-text="errorMessage"> </div>
            <input placeholder = "Enter your name..." style="width: 250px;" name="name" autocomplete="off"
                autocomplete="nope" class="form-control border-secondary player-profile__input d-inline"
                v-model="userProfile.name">
            <br>
            <input placeholder = "Enter your company name..." style="width: 300px;"
                class="form-control border-secondary player-profile__input d-inline me-3"
                v-model="organizer.companyName" autocomplete="off" autocomplete="nope">
            <a type="submit" data-url="{{ route('organizer.profile.update') }}" v-on:click="submitEditProfile(event);"
                style="border-color: green;"
                class="mt-4 oceans-gaming-default-button simple-button cursor-pointer bg-success btn-success btn text-dark px-3 py-1 me-2 text-success">
                Save
            </a>
            <svg {{-- Close icon --}} v-on:click="reset();isEditMode = false;" xmlns="http://www.w3.org/2000/svg"
                width="28" height="28" fill="currentColor"
                class="mt-4 py-1 bi bi-x-circle cursor-pointer text-red" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
            </svg>
            <br>
        </div>
        <div v-show="!isEditMode" class="user-select-none ">
            <h5>
                {{ $userProfile->name }}
            </h5>
            <div class="my-2">
                <template v-if="organizer.industry && organizer.industry!= ''">
                    <span>
                        <span class="me-2"> </span>
                        <span class="me-3" v-text="organizer.industry"> </span>
                    </span>
                </template>
                <span class="me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                </span>
                <span class="d-inline p-0 m-0" style="display: inline !important;">
                    <span data-follower-stats="{{ $followersCount }}" class="cursor-pointer d-inline ps-0"
                        onclick="openModal('followers')">
                        <span data-count="{{ $followersCount }}" @class(['followCounts' . $userProfile->id])> {{ $followersCount }}
                            follower{{ bldPlural($followersCount) }}
                        </span>
                    </span>
                </span>
                @include('includes.Profile.ProfileStatsModal', [
                    'propsTeamOrUserId' => $userProfile->id,
                    'propsUserId' => $loggedUserId ?? '0',
                    'propsIsUserSame' => $isUserSame ? 1 : 0,
                    'propsRole' => 'ORGANIZER',
                    'propsUserRole' => $loggedUserRole,
                ])

            </div>
            @if ($isOwnProfile)
                <div class="text-center">
                    <button v-on:click="reset(); isEditMode = true;"
                        v-if="!isEditMode"
                        class="oceans-gaming-default-button oceans-gaming-primary-button py-1 px-5 me-3">
                        Edit
                    </button>
                </div>
            @else
                <div class="text-center">
                    <form id="followFormProfile" method="POST" class="d-inline me-3 followFormProfile"
                        action="{{ route('participant.organizer.follow') }}">
                        @csrf
                        @auth
                            <input type="hidden" name="role" value="{{ $user && $user->id ? $user->role : '' }}">
                            <input type="hidden" name="user_id" value="{{ $user && $user->id ? $user->id : '' }}">
                            <input type="hidden" name="organizer_id" value="{{ $userProfile?->id }}">
                        @endauth
                        @guest
                            <button type="button" @class(['rounded px-3 py-2 ', 'followButton' . $userProfile->id])
                                onclick="reddirectToLoginWithIntened('{{ route('public.organizer.view', ['id' => $userProfile->id]) }}')"
                                id="followButton" style="background-color: #43A4D7; color: white; border: none;">
                                Follow
                            </button>
                        @endguest
                        @auth
                            @if ($user->role == 'PARTICIPANT')
                                <button type="submit" id="followButton" @class(['rounded px-3 py-2 ', 'followButton' . $userProfile->id])
                                    style="background-color: {{ $user && $userProfile?->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $user && $userProfile?->isFollowing ? 'black' : 'white' }}; border: none;">
                                    {{ $user && $userProfile?->isFollowing ? 'Following' : 'Follow' }}
                                </button>
                            @else
                                <button type="button" @class(['rounded px-3 py-2 ', 'followButton' . $userProfile->id])
                                    onclick="toastWarningAboutRole(this, 'Participants can follow only!');"
                                    id="followButton" style="background-color: #43A4D7; color: white; border: none;">
                                    Follow
                                </button>
                            @endif
                        @endauth
                    </form>
                    <a href="{{ route('user.message.view', ['userId' => $userProfile->id]) }}">
                        <button
                            class="oceans-gaming-default-button oceans-gaming-transparent-button bg-light border-0 rounded px-3 py-2">
                            Message
                        </button>
                    </a>
                </div>
            @endif
            <br>
        </div>
    </div>
</div>
