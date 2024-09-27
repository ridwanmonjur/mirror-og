@php
    use Carbon\Carbon;
@endphp
@if ($currentTab == $name)
    <div class="tab-size">
        @if (!isset($data[0]))
            <p class="text-center mt-5"> You have no users in this list. </p>
        @else
            <table id="{{'table' . $name}}" style="max-width: 80%; margin: auto;" class="table responsive  table-striped bg-white px-3">
                <tbody>
                    @foreach ($data as $member)
                        <tr wire:key="{{ $name . $member->id }}" class="st">
                            <td class="colorless-col">
                                <svg onclick="redirectToProfilePage({{ $member->{$propertyName}->id }});" class="gear-icon-btn"
                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                    class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            </td>
                            <td class="coloured-cell px-3">
                                <div class="player-info cursor-pointer">
                                    <img class="rounded-circle d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/' . $member->{$propertyName}->userBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} height="40" width="40"
                                    >
                                    <span>{{ $member->{$propertyName}->name }}</span>
                                </div>
                            </td>
                            <td class="flag-cell coloured-cell px-3">
                                <span>{{ $member->{$propertyName}->email }}</span>
                            </td>
                            <td class="coloured-cell px-3"> {{ $member->{$propertyName}->name }} </td>
                            <td class="coloured-cell ps-3 text-end">
                                {{ is_null($member->updated_at) ? '-' : Carbon::parse($member->updated_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <br>
        <div style="max-width: 80%; margin: auto;">
            {{ $data->links() }}
        </div>
    </div>
@else
    <span class="d-none"> </span>
@endif
<script>
new ResponsiveTable({{'#table' . $name}}, 'stack', '600px');
</script>