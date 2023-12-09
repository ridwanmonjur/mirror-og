<?php

use Carbon\Carbon;

function bladeEventStatusStyleMapping($status)
{
    $mappingEventState = config('constants.mappingEventState');
    $status = $status ?? 'LIVE';
    $stylesEventStatus = '';
    $stylesEventStatus .= 'background-color: ' . $mappingEventState[$status]['buttonBackgroundColor'] . ' ;';
    $stylesEventStatus .= 'color: ' . $mappingEventState[$status]['buttonTextColor'] . ' ; ';
    $stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$status]['borderColor'] . ' ; ';
    return $stylesEventStatus;
}

function bladeEventRatioStyleMapping($registeredParticipants, $totalParticipants)
{
    $stylesEventRatio = '';
    if ($totalParticipants == 0) {
        $ratio = 0;
    } else {
        $ratio = (float) $registeredParticipants / $totalParticipants;
    }
    if ($ratio > 0.9) {
        $stylesEventRatio .= "background-color: red; color: white;";
    } elseif ($ratio == 0) {
        $stylesEventRatio .= "background-color: #8CCD39; color: white;";
    } elseif ($ratio > 0.5) {
        $stylesEventRatio .= "background-color: #FA831F; color: white;";
    } elseif ($ratio <= 0.5) {
        $stylesEventRatio .= "background-color: #FFE325; color: black;";
    }
    return $stylesEventRatio;
}

function bladeGenerateEventStartEndDateStr($startDate, $startTime)
{
    if ($startDate != null && $startTime != null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i:s', $startDate . ' ' . $startTime, 'UTC') ?? null;
        $carbonDateTimeUtc = $carbonDateTimeUtc->setTimezone('Asia/Singapore');
        $datePart = $carbonDateTimeUtc->format('Y-m-d');
        $timePart = $carbonDateTimeUtc->isoFormat('h:mm a');
        $dayStr = $carbonDateTimeUtc->englishDayOfWeek;
        $dateStr = $datePart . ' ' . $timePart;
        $combinedStr = $datePart . ' (' . $dayStr . ')';
    } else {
        $datePart = 'Not set';
        $timePart = 'Not set';
        $dayStr = '';
        $dateStr = 'Please enter dae and time';
        $combinedStr = 'Not set';
    }
    return [
        'datePart' => $datePart,
        '$dateStr' => $dateStr,
        'timePart' => $timePart,
        'dayStr' => $dayStr,
        'combinedStr' => $combinedStr
    ];
}


function bladeEventTierImage($eventTier)
{

    // TODO PUT THESE IN STORAGE
    if ($eventTier) {
        $eventTierLower = strtolower($eventTier);
        $eventTierLowerImg = asset('/assets/images/' . $eventTierLower . '.png');
    } else {
        $eventTierLowerImg = asset('assets/images/createEvent/question.png');
    }
    return $eventTierLowerImg;
}

function bladeImageNull($eventBanner)
{
    if ($eventBanner) {
        $eventBannerImg = asset('storage/' . $eventBanner);
    } else {
        $eventBannerImg = asset('assets/images/createEvent/question.png');
    }
    return $eventBannerImg;
}

function trustedBladeHandleImageFailure(){
    $imgFailure = asset('assets/images/broken-image.jpeg');
    return "onerror=\"this.onerror=null;this.height='200';this.width='200';this.src='$imgFailure'\";";    
}

function bladeEventGameImage($eventBanner)
{
    if ($eventBanner) {
        $eventBannerImg = asset('storage/' . $eventBanner);
    } else {
        $eventBannerImg = asset('assets/images/createEvent/question.png');
    }
    return $eventBannerImg;
}