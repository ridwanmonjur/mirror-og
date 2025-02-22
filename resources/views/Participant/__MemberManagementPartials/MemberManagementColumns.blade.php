<div class="col-12 col-lg-1 ">
    <img onclick="redirectToProfilePage({{ $member->user_id }});" width="45" height="45"
        src="{{ bladeImageNull($member->user->userBanner) }}"
        {!! trustedBladeHandleImageFailure() !!}
        class="me-2 my-3 random-color-circle cursor-pointer rounded-circle object-fit-cover">
</div>

<div class="col-12 col-lg-9  py-2">
    <div onclick="redirectToProfilePage({{ $member->user_id }});">
        <div class="py-0 my-0">
            <h6 style="width: 25ch;" class="text-truncate my-0 mb-0 pt-2 pb-0 d-inline-block"
                
            >
             @if ($captain && $member->id == $captain->team_member_id)
                <img onclick="deleteCaptain({{ $member->id }}, {{ $selectTeam->id }})"
                    class="z-99 d-inline-block rounded-pill mx-2  cursor-pointer captain-crown" height="20" width="20"
                    src="/assets/images/participants/crown-straight.png"
                >
            @endif
            <u onclick="redirectToProfilePage({{ $member->user_id }});"
                class="cursor-pointer"
            >{{ $member->user->name }}</u></h6>
        </div>
        <div>
            <span class="fs-4 me-0 ">
                {{ $member?->user?->participant?->region_flag  }}
            </span>
            <span>{{$actorStatusMap[$member->status][$member->actor]}}</span>
            <span>{{ $member->updatedAtDiffForHumans() }}</span>
        </div>
    </div>
</div>
