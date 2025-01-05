@auth
    @if ($user->id != $userProfile->id)

        @if ($friend && $user->role == "PARTICIPANT")
            @if ($friend->status == 'pending')
                @if ($friend->actor_id != $userProfile->id)
                    <span class="d-flex justify-content-start align-items-center mt-1">
                        <button
                            onclick="formRequestSubmitById('Are you sure you want to delete this request?' ,'deleteFriendRequest')"
                            class="me-2 btn bg-success border-none rounded-pill px-2 py-2"
                            style="font-size: 1rem; font-weight: lighter; color: black;">
                            <svg class="mt-1 cursor-pointer mb-1" xmlns="http://www.w3.org/2000/svg" width="18"
                                height="18" fill="black" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path
                                    d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                            </svg>
                            <span class="me-1"> Friend request sent </span>
                        </button>

                    </span>
                @else
                    <span style="font-size: 1rem; font-weight: lighter;" class="me-3 mt-1 pt-2">
                        <u>Friend request received</u>
                    </span>
                    <button type="button"
                        onclick="formRequestSubmitById('Are you sure you want to accept this friend request?' ,'acceptFriendRequest')"
                        class="btn bg-success text-dark px-4 py-2 me-2 rounded-pill mt-1">
                        <span>Accept</span>
                    </button>
                    <button type="button"
                        onclick="formRequestSubmitById('Are you sure you want to reject this friend request?' ,'rejectFriendRequest')"
                        class="btn btn-light text-red border-danger px-4 py-2 me-2 rounded-pill mt-1">
                        <span class="">Reject</span>
                    </button>
                @endif
            @elseif ($friend->status == 'accepted')
                <div class="d-flex justify-content-start align-items-center dropdown-show mt-1">
                    <button type="button" style="background: #D8DADF" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        class="bg-success rounded-pill btn text-dark px-3 py-2  me-2 dropdown-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person-check-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>
                        <span>Friends</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-3 bi bi-chevron-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item cursor-pointer  px-4 py-2" 
                            onclick="formRequestSubmitById('Are you sure you want to remove this person as your friend?' ,'leftFriendRequest')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708" />
                            </svg>
                            <span> Remove friendship</span>
                        </a>
                    </div>
                </div>
            @elseif ($friend->status == 'rejected')
                <button type="button" style="background: #D8DADF" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @class([
                        'rounded-pill bg-light border-dark text-dark btn text-dark px-2 py-2 me-2 mt-1',
                        'dropdown-toggle' => $friend->actor_id == $user->id,
                    ])>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708" />
                    </svg>
                    <span>Didn't accept</span>
                </button>
                @if ($friend->actor_id == $user->id)
                    <div class="dropdown-menu py-0 mt-1" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item cursor-pointer  px-4 " style="padding-top: 12px; padding-bottom: 12px;"
                            onclick="formRequestSubmitById('Are you sure you want to accept this person as your friend?' ,'acceptFriendRequest')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-check-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            <span> Accept friendship</span>
                        </a>
                    </div>
                @endif
            @elseif ($friend->status == 'left')
                <button type="button" style="background: #D8DADF" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @class([
                        'rounded-pill bg-light border-dark text-dark btn text-dark px-3 py-2 me-2 mt-1',
                        'dropdown-toggle' => $friend->actor_id == $user->id,
                    ])>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708" />
                    </svg>
                    <span>Not friends</span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-3 bi bi-chevron-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                        </svg>
                </button>
                @if ($friend->actor_id == $user->id)
                    <div class="dropdown-menu py-0 mt-1" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item cursor-pointer  cursor-pointer py-2 px-4 "
                            onclick="formRequestSubmitById('Are you sure you want to accept this person as your friend?' ,'acceptFriendRequest')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-check-fill me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            <span> Accept friendship</span>
                        </a>
                    </div>
                @endif
            @endif
        @endif

        @if (!$isUserSame && !$friend && $user->role == "PARTICIPANT")
            <button type="button"
                onclick="formRequestSubmitById('Are you sure you want to send this friend request?' ,'sendFriendRequest')"
                class="btn btn-primary rounded-pill text-light px-3 py-2 me-2 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                    class="bi bi-person-plus me-1" viewBox="0 0 16 16">
                    <path
                        d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                    <path fill-rule="evenodd"
                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5" />
                </svg>
                <span> Add friend</span>
            </button>
        @endif
        @if ($user->role == "PARTICIPANT")
            @if ($isFollowingParticipant)
                <button type="button" onclick="formRequestSubmitById(null ,'sendFollowRequest')"
                    class="btn btn-success rounded-pill text-dark px-3 py-2 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-sunglasses" viewBox="0 0 16 16">
                    <path d="M3 5a2 2 0 0 0-2 2v.5H.5a.5.5 0 0 0 0 1H1V9a2 2 0 0 0 2 2h1a3 3 0 0 0 3-3 1 1 0 1 1 2 0 3 3 0 0 0 3 3h1a2 2 0 0 0 2-2v-.5h.5a.5.5 0 0 0 0-1H15V7a2 2 0 0 0-2-2h-2a2 2 0 0 0-1.888 1.338A2 2 0 0 0 8 6a2 2 0 0 0-1.112.338A2 2 0 0 0 5 5zm0 1h.941c.264 0 .348.356.112.474l-.457.228a2 2 0 0 0-.894.894l-.228.457C2.356 8.289 2 8.205 2 7.94V7a1 1 0 0 1 1-1"/>
                    </svg>
                    <span> Following </span>
                </button>
            @else
                <button type="button" onclick="formRequestSubmitById(null ,'sendFollowRequest')"
                    class="btn btn-primary rounded-pill text-light px-3 py-2 mt-1">
                   <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eyeglasses" viewBox="0 0 16 16">
                    <path d="M4 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4m2.625.547a3 3 0 0 0-5.584.953H.5a.5.5 0 0 0 0 1h.541A3 3 0 0 0 7 8a1 1 0 0 1 2 0 3 3 0 0 0 5.959.5h.541a.5.5 0 0 0 0-1h-.541a3 3 0 0 0-5.584-.953A2 2 0 0 0 8 6c-.532 0-1.016.208-1.375.547M14 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/>
                    </svg>
                    <span> Follow </span>
                </button>
            @endif
        @endif
        <a href="{{route('user.message.view', ['userId' => $userProfile->id] )}}">
            <button 
                x-on:click="startMessage" 
                type="button" class="btn btn-light rounded-pill border-dark text-dark px-2 py-2 ms-2 mt-1"
            >
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-chat-dots me-1" viewBox="0 0 16 16">
                    <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                    <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9 9 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.4 10.4 0 0 1-.524 2.318l-.003.011a11 11 0 0 1-.244.637c-.079.186.074.394.273.362a22 22 0 0 0 .693-.125m.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6-3.004 6-7 6a8 8 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a11 11 0 0 0 .398-2"/>
                    </svg>
                    <span> Message </span>
            </button>
        </a>
    @else
        <br>
    @endif
@endauth
