<h5 class="mb-3"><u>Teams</u></h5>
<div class="pb-5" id="current-teams">
    @if (isset($teamList[0]))
        <div class="row row-cols-1   gy-2 gx-4">
            @foreach ($teamList as $team)
                <div class="col">
                    <div class="card h-100 border-0" style="transition: transform 0.2s; cursor: pointer;"
                        onmouseover="this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div class="card-body border border-2">
                            <div class="row">
                                <div class="col-10 d-flex justify-content-start">
                                    <img src="{{ '/storage' . '/' . $team->teamBanner }}" {!! bldImgF() !!}
                                        class="border object-fit-cover my-2 border-secondary  rounded-circle me-3"
                                        width="50" height="50" alt="{{ $team->teamName }}">
                                    <div>
                                        <p class="card-title py-0 my-0  d-inline-block  text-wrap my-0 py-0 mb-0"><u
                                                class="me-2">{{ $team->teamName }}</u><span
                                                style="font-size: 1.5rem;">{{ $team->country_flag }}</span>
                                                <span class="fw-bold  fs-7 text-muted">{{ $team->country_name }}</span></p>
                                        <div class="text-muted text-wrap align-middle">
                                            <span class="me-2">{{ $team->createdAtHumaReadable() }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('public.team.view', ['id' => $team->id, 'title' => $team->slug]) }}"
                                        class="btn btn-link position-relative  btn-sm gear-icon-btn "
                                        style="z-index: 3;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 6L15 12L9 18" stroke="gray" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </div>

                            </div>
                        </div>
                        <a href="{{ route('public.team.view', ['id' => $team->id, 'title' => $team->slug]) }}"
                            class="position-absolute top-0 start-0 w-100 h-100"></a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div>
            <div class="text-start pt-2">
                <svg class="ms-4" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="d-inline text-body-secondary text-center  mb-0">No teams confirmed registration yet.</p>
            </div>
        </div>
    @endif
</div>
