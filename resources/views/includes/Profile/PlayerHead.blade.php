<div id="backgroundBanner" class="member-section px-2 pt-2" @vue:mounted="init" v-scope="ParticipantData()">
    @if ($isOwnProfile)
        <input type="hidden" id="userBannerInput" value="{{ $userProfile->userBanner }}">
        <input type="hidden" id="backgroundColorInput" value="{{ $userProfile->profile?->backgroundColor }}">
        <input type="hidden" id="fontColorInput" value="{{ $userProfile->profile?->fontColor }}">
    @endif
    <input type="hidden" id="activity_input" value="{{ json_encode($activityNames) }}">
    <input type="hidden" id="region_details_input"
        value="{{ json_encode($userProfile->participant?->getRegionDetails()) }}">
    <input type="hidden" id="initialUserData" value="{{ json_encode($userProfile) }}">
    <input type="hidden" id="initialParticipantData" value="{{ json_encode($userProfile->participant) }}">
    <div class="d-flex justify-content-end align-items-center py-0 my-0 mb-2 mx-3 flex-wrap">
        @if ($isUserSame)
            <input type="file" id="backgroundInput" accept="image/*" class="d-none">
            <button data-bs-toggle="offcanvas" data-bs-target="#profileDrawer" v-on:click="isEditMode=false" v-cloak
                v-show="!isEditMode" {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                class="btn btn-secondary text-light rounded-pill py-2 me-3 fs-7">
                Change Background
            </button>
            <button v-cloak v-show="!isEditMode" v-on:click="reset(); isEditMode = true;"
                class="oceans-gaming-default-button oceans-gaming-primary-button px-3 py-2 fs-7">
                Edit Profile
            </button>
            <button v-cloak v-show="isEditMode" v-on:click="submitEditProfile(event)"
                data-url="{{ route('participant.profile.update') }}"
                :style="{ color: user.fontColor, borderColor: user.fontColor }"
                class="rounded-pill btn cursor-pointer px-4 py-2 me-3 fs-7">
                Save
            </button>
            {{-- Close icon --}}
            <svg v-cloak v-if="isEditMode" v-on:click="restoreAfterEditMode()" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="currentColor" class="bi bi-x-circle cursor-pointer align-middle "
                viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                <path
                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
            </svg>
        @endif
        @if (!$isOwnProfile && !$isUserSame)
            @include('includes.Profile.FriendFriendUI')
        @endif
    </div>
    <div class="d-flex justify-content-center align-items-center flex-wrap">
        <div class="member-image align-middle">
            <div class="upload-container  motion-logo">
                <label class="upload-label">
                    <div class="circle-container">
                        <div class="uploaded-image"
                            style="background-image: url({{ '/storage' . '/' . $userProfile->userBanner }} ); background-size: cover; 
                                        z-index: 99; background-repeat: no-repeat; background-position: center; {{ $frameStyles }}">
                        </div>
                        <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                            <a aria-hidden="true" data-fslightbox="lightbox"
                                data-href="{{ '/' . 'storage/' . $userProfile->userBanner }}"
                                class="hyperlink-lightbox">
                                <button class="btn simple-button btn-sm p-0 "><svg xmlns="http://www.w3.org/2000/svg"
                                        width="20" height="20" fill="currentColor" class="bi bi-eye"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                        <path
                                            d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                    </svg>
                                </button>
                            </a>
                            @if ($isUserSame)
                                <button v-cloak v-show="isEditMode" id="upload-button2" class="btn btn-sm simple-button mx-2 p-0"
                                    aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path
                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd"
                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                </button>
                                <button v-cloak v-if="isEditMode" v-on:click="removeProfile(event);" id="trash-button3" class="btn btn-sm simple-button  p-0"
                                    aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </label>

            </div>
        </div>
        <div class="member-details user-select-none ">
            <div v-cloak v-show="errorMessage != null && isEditMode" class="text-red" v-text="errorMessage"> </div>
            <div v-cloak v-show="isEditMode" style="color: black;">
                <input 
                        placeholder="Your username." 
                        style="width: 250px;"
                        type="text"
                        autocomplete="off"
                        class="form-control border-secondary player-profile__input d-inline me-3" 
                        v-model="user.name"
                        :style="{ color: user.fontColor  }"
                    >
                <input type="file" id="image-upload" accept=".png, .jpg, .jpeg, image/png, image/jpeg" v-cloak
                    v-show="isEditMode" class="d-none">
                <br>
                <span class="d-inline-flex flex-wrap justify-content-start align-items-center">
                    <input placeholder = "Enter your real name." 
                        style="width: min(200px, 60vw);" 
                         autocomplete="off"
                        autocomplete="nope" class="form-control border-secondary player-profile__input me-3 d-inline"
                        v-model="participant.nickname"
                        :style="{ color: user.fontColor  }"
    
                    >
                    
                    <input placeholder="Birthday" type="date" style="width: min(150px, 60vw);"
                        default="1999-05-05" id="birthdate"
                        class="form-control custom-date-input border-secondary player-profile__input d-inline me-2"
                        v-model="participant.birthday"
                        :style="{ color: user.fontColor , '--user-font-color': user.fontColor  }"

                    >
                    <template v-if="!participant.isAgeVisible">
                        {{-- Eye invisible icon --}}
                        <svg v-on:click="participant.isAgeVisible = true" xmlns="http://www.w3.org/2000/svg"
                            width="20" height="20" v-bind:fill="user.fontColor"
                            class="bi bi-eye-slash-fill cursor-pointer" viewBox="0 0 16 16">
                            <path
                                d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z" />
                            <path
                                d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z" />
                        </svg>
                    </template>
                    <template v-else>
                        {{-- Eye visible --}}
                        <svg v-on:click="participant.isAgeVisible = false" xmlns="http://www.w3.org/2000/svg"
                            width="20" height="20" v-bind:fill="user.fontColor" class="bi bi-eye-fill cursor-pointer"
                            viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                            <path
                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                        </svg>
                    </template>
                </span>
                <br>
                <input placeholder = "Write a description" style="width: min(80vw, 370px);" type="text"
                    autocomplete="off" autocomplete="nope"
                    class="mb-2 form-control border-secondary player-profile__input d-inline me-3"
                    v-model="participant.bio"
                    :style="{ color: user.fontColor  }"    
                >
                <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                    <span class="me-3 d-flex justify-content-center align-items-center">
                        <svg class="me-2 align-middle" xmlns="http://www.w3.org/2000/svg" width="16"
                            height="16" v-bind:fill="user.fontColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                        </svg>
                        <template v-if="countries">
                            <select 
                                 :style="{ color: user.fontColor, backgroundColor: user.backgroundColor , borderColor: user.fontColor }"  
                                v-on:change="changeFlagEmoji" id="select2-country3" style="width: 250px;"
                                
                                class="d-inline-block text-truncate text-nowrap form-select" data-placeholder="Select a country"
                                v-bind:value="participant.region || ''"
                                v-bind:name="participant.region"
                            >
                        </template>
                    </span>
                    <span class="me-3">
                        <svg class="align-middle" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            v-bind:fill="user.fontColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                            <path
                                d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z" />
                            <path
                                d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z" />
                        </svg>
                        <input style="width: min(180px, 60vw);" placeholder = "Enter your domain..."
                            autocomplete="off" autocomplete="nope"
                            class="form-control border-secondary player-profile__input d-inline"
                            v-model="participant.domain"
                            :style="{ color: user.fontColor  }"    
                        >
                    </span>
                    <br> <br> <br>
                    <span>
                        <svg class="align-middle" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            v-bind:fill="user.fontColor" class="bi bi-person" viewBox="0 0 16 16">
                            <path
                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                        </svg>
                        <span :style="{ color: user.fontColor  }">Joined {{ $userProfile->createdIsoFormat() }}</span>
                    </span>

                </div>
            </div>
            <div v-cloak v-show="!isEditMode" class="ms-2">
                <br>
                @if ($userProfile->participant?->nickname)
                    <div class="d-flex justify-content-start align-items-center flex-wrap">
                        <h4 class="my-0 me-4">{{ $userProfile->participant->nickname }}</h4>
                    </div>
                @else
                    <div class="d-flex justify-content-start align-items-center flex-wrap">
                        <h4 class="my-0 me-4">{{ $userProfile->name }}</h4>
                    </div>
                @endif
                <div class="my-2">
                    @if ($userProfile->participant?->nickname)
                        <span>{{ $userProfile->name }}</span>
                    @endif
                    @if (
                        $userProfile->participant?->birthday &&
                            $userProfile->participant->nickname &&
                            $userProfile->participant?->isAgeVisible &&
                            $userProfile->participant->age)
                        <span style="margin-left: -5px;">,</span>
                    @endif

                    @if ($userProfile->participant?->birthday)
                        <span>
                            @if ($userProfile->participant?->isAgeVisible)
                                <span>{{ $userProfile->participant->age }}</span>
                            @endif
                            {{-- Calendar --}}
                            <svg @class([
                                'ms-4' =>
                                    ($userProfile->participant->age &&
                                        $userProfile->participant->isAgeVisible) ||
                                    $userProfile->participant->nickname,
                            ]) xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            @if ($userProfile->participant?->birthday)
                                <span class="me-2">{{ $userProfile->participant->birthday }}</span>
                            @endif
                            @if ($isOwnProfile)
                                @if ($userProfile->participant?->isAgeVisible)
                                    {{-- Eye visible icon --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path
                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                @else
                                    {{-- Eye invisible icon --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                        <path
                                            d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z" />
                                        <path
                                            d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z" />
                                    </svg>
                                @endif
                            @endif
                        </span>
                    @endif
                </div>

                @if ($userProfile->participant?->bio)
                    <p>{{ $userProfile->participant->bio }}</p>
                @endif

                <div class="d-flex justify-content-start flex-wrap w-100">
                    @if ($userProfile->participant?->region_name)
                        <span class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span class="me-1">{{ $userProfile->participant->region_name }}</span>
                        </span>
                    @endif
                    @if ($userProfile->participant?->domain)
                        <span class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-link-45deg me-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z" />
                                <path
                                    d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z" />
                            </svg>
                            <span class="me-1">{{ $userProfile->participant->domain }}</span>
                        </span>
                    @endif
                    <span class="me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person me-2" viewBox="0 0 16 16">
                            <path
                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                        </svg>
                        <span>Joined
                            {{ $userProfile->createdIsoFormat() }}</span>
                    </span>
                </div>
                
                <div class="mt-4 my-2 text-wrap">
                    <div @vue:mounted="init"
                        v-scope="ProfileCount(
                                '{{ $userProfile->id }}', '{{ $userProfile->role }}'
                            )"
                        class="mt-4">
                        <!-- Tabs -->
                        <div v-if="!loading">
                            <div class="" style="color: inherit !important;">
                                <span v-bind:data-follower-stats="count['followers']"
                                    class=" cursor-pointer user-select-none ps-0 me-4"
                                    v-on:click="openModal('followers')">
                                    <span v-text="count['followers']  ?? '0'"> </span> Follower<span
                                        v-text="count['followers'] > 1 || count['followers'] === 0 ? 's' : ''"></span>
                                </span>
                                <span v-bind:data-following-stats="count['following']"
                                    class=" cursor-pointer user-select-none ps-0 me-4"
                                    v-on:click="openModal('following')">
                                    <span v-text="count['following'] ?? '0'"> </span>
                                    Following
                                </span>
                                <span v-bind:data-friends-stats="count['friends']"
                                    class="cursor-pointer user-select-none ps-0 me-4"
                                    v-on:click="openModal('friends')">
                                    <span v-text="count['friends'] ?? '0'"> </span>
                                    Friend<span
                                        v-text="count['friends'] > 1 || count['friends'] === 0? 's' : ''"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    @include('includes.Profile.ProfileStatsModal', [
                        'propsTeamOrUserId' => $userProfile->id,
                        'propsUserId' => $loggedUserId ?? '0',
                        'propsIsUserSame' => $isUserSame ? 1 : 0,
                        'propsRole' => 'PARTICIPANT',
                        'propsUserRole' => $loggedUserRole,
                    ])
                </div>
                <br><br>

            </div>
        </div>
    </div>
</div>

