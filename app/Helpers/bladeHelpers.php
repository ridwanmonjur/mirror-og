<?php

use Carbon\Carbon;
use Illuminate\Support\Str;

// function bladeGetPaymentLogos($logoType)
// {
//     $logoName = [
//         'bank' => 'bankLogos',
//         'eWallet' => 'eWalletLogos',
//         'otherEWallet' => 'otherEWalletLogos',
//         'card' => 'cardLogos',
//     ];

//     $logo = $logoName[$logoType];

//     return config("constants.{$logo}");
// }

function fixTimeToRemoveSeconds($time)
{
    if ($time === null) {
        return null;
    }
    if (substr_count($time, ':') === 2) {
        $time = explode(':', $time);
        $time = $time[0].':'.$time[1];
    }

    return $time;
}

function bladeSlug ($title) {
    return $title ? Str::slug($title) : '';
}

function bladeEventRatioStyleMapping($registeredParticipants, $totalParticipants)
{
    $stylesEventRatio = '';

    if ($totalParticipants === 0 || $totalParticipants === null) {
        $ratio = 0;
    } else {
        $ratio = (float) $registeredParticipants / $totalParticipants;
    }

    if ($ratio > 0.9) {
        $stylesEventRatio .= 'background-color: #EF4444; color: white;';
    } elseif ($ratio === 0) {
        $stylesEventRatio .= 'background-color: #f9b82a; color: white;';
    } elseif ($ratio > 0.5) {
        $stylesEventRatio .= 'background-color: #FA831F; color: white;';
    } elseif ($ratio <= 0.5) {
        $stylesEventRatio .= 'background-color: #FFE325; color: #2e4b59;';
    }

    return $stylesEventRatio;
}

function bladeGenerateEventStartEndDateStr($startDate, $startTime)
{
    $startTime = fixTimeToRemoveSeconds($startTime);
    if ($startDate !== null && $startTime !== null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$startTime, 'UTC') ?? null;
        // $carbonDateTimeUtc = $carbonDateTimeUtc->setTimezone('Asia/Singapore');
        $datePart = $carbonDateTimeUtc->format('Y-m-d');
        $timePart = $carbonDateTimeUtc->isoFormat('h:mm a');
        $dayStr = $carbonDateTimeUtc->englishDayOfWeek;
        $dateStr = $datePart.' '.$timePart;
        $combinedStr = $datePart.' ('.$dayStr.')';
    } else {
        $datePart = 'Date is not set';
        $timePart = 'Time is not set';
        $dayStr = '';
        $dateStr = 'Please enter date and time';
        $combinedStr = 'Date/time is not set';
    }

    return [
        'datePart' => $datePart,
        'dateStr' => $dateStr,
        'timePart' => $timePart,
        'dayStr' => $dayStr,
        'combinedStr' => $combinedStr,
    ];
}



function bladeImageNull($eventBanner)
{
    $imgFailure = asset('assets/images/404.png');
    if ($eventBanner) {
        $eventBannerImg = asset('storage/'.$eventBanner);
    } else {
        $eventBannerImg = $imgFailure;
    }

    return $eventBannerImg;
}

function bladeImageNullq($eventBanner)
{
    $imgFailure = asset('assets/images/404q.png');
    if ($eventBanner) {
        $eventBannerImg = asset('storage/'.$eventBanner);
    } else {
        $eventBannerImg = $imgFailure;
    }

    return $eventBannerImg;
}

function trustedBladeHandleImageFailure()
{
    $imgFailure = asset('assets/images/404.png');

    return "onerror=\"this.onerror=null;this.src='{$imgFailure}';\"";
}

function trustedBladeHandleImageFailureResize()
{
    $imgFailure = asset('assets/images/404.png');

    return "onerror=\"this.onerror=null;this.width='500px';this.height='50px';this.src='{$imgFailure}';\"";
}

function trustedBladeHandleImageFailureBanner()
{
    $imgFailure = asset('assets/images/404.png');

    return "onerror=\"this.onerror=null;this.src='{$imgFailure}';\"";
}

function bladeEventGameImage($eventBanner)
{
    if ($eventBanner) {
        $eventBannerImg = asset('storage/'.$eventBanner);
    } else {
        $eventBannerImg = asset('assets/images/createEvent/question.png');
    }

    return $eventBannerImg;
}

function bladeEventTowerLowerClass($eventTier)
{
    return $eventTier ? strtolower($eventTier) : 'no-tier';
}

function bladeOrdinalPrefix($number)
{
    $number = intval($number);

    if ($number % 100 >= 11 && $number % 100 <= 13) {
        return $number.'th';
    }

    switch ($number % 10) {
        case 1:
            return $number.'st';
        case 2:
            return $number.'nd';
        case 3:
            return $number.'rd';
        default:
            return $number.'th';
    }
}

function bladePluralPrefix($amount, $singular = '', $plural = 's')
{
    if ($amount === 1) {
        return $singular;
    }

    return $plural;
}
