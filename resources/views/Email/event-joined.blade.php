@component('mail::message')
<table style="width: 100%; background-color: #f3f4f6; padding: 20px;">
    <tr>
        <td align="center">
            <img src="{{ asset('assets/images/logo-default.png') }}" alt="Ocean's Gaming Logo" style="max-width: 200px;">
        </td>
    </tr>
</table>

# {{ $greeting }}

{{ $body }}

@isset($actionText)
    @component('mail::button', ['url' => $actionUrl])
        {{ $actionText }}
    @endcomponent
@endisset

{{ $salutation }}

@isset($additionalLines)
    @foreach ($additionalLines as $line)
        {{ $line }}
    @endforeach
@endisset
@endcomponent
