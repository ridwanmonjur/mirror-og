<div class="col-12 col-lg-10 d-flex justify-content-start align-items-center">
    <img onclick="redirectToProfilePage({{ $member->user_id }}, '{{ $member->user->slug }}');" width="45" height="45"
        src="{{ bladeImageNull($member->user->userBanner) }}"
        {!! trustedBladeHandleImageFailure() !!}
        class="me-3 random-color-circle cursor-pointer rounded-circle object-fit-cover">

    <div >
        <div class="py-0 my-0">
            <h6  class="text-wrap my-0 mb-0 pt-2 pb-0 d-inline-block"
            >
             @if ($captain && $member->id == $captain->team_member_id)
                <img onclick="deleteCaptain({{ $member->id }}, {{ $selectTeam->id }})"
                    class="z-99 d-inline-block rounded-pill mx-2  cursor-pointer captain-crown" height="20" width="20"
                    src="/assets/images/participants/crown-straight.png"
                >
            @endif
            <u onclick="redirectToProfilePage({{ $member->user_id }}, '{{ $member->user->slug }}');"
                class="cursor-pointer"
            >{{ $member->user->name }}</u></h6>
             <span class="fs-5 ms-2 ">
                {{ $member?->user?->participant?->region_flag  }}
            </span>
        </div>
        <div>
           
            <span>{{$actorStatusMap[$member->status][$member->actor]}}</span>
            <span>{{ $member->updatedAtDiffForHumans() }}</span>
        </div>
    </div>
</div>
