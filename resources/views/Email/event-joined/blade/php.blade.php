@component('mail::message')
{{-- Background and Logo --}}
<table style="width: 100%; background-color: #f3f4f6; padding: 20px;">
    <tr>
        <td align="center">
            <img src="{{ asset('path_to_your_logo.png') }}" alt="Ocean's Gaming Logo" style="max-width: 200px;">
        </td>
    </tr>
</table>

{{-- Greeting --}}
# {{ $greeting }}

{{-- Body --}}
{{ $body }}

{{-- Action Button --}}
@isset($actionText)
    @component('mail::button', ['url' => $actionUrl])
        {{ $actionText }}
    @endcomponent
@endisset

{{-- Salutation --}}
{{ $salutation }}

{{-- Additional Lines --}}
@isset($additionalLines)
    @foreach ($additionalLines as $line)
        {{ $line }}
    @endforeach
@endisset
@endcomponent
