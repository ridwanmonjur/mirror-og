<?php

use Carbon\Carbon;

function bladeEventStatusStyleMapping($status)
{
    $mappingEventState = config('constants.mappingEventState');
    $status = $status ?? 'DRAFT';
    $stylesEventStatus = '';
    $stylesEventStatus .= 'background-color: ' . $mappingEventState[$status]['buttonBackgroundColor'] . ' ;';
    $stylesEventStatus .= 'color: ' . $mappingEventState[$status]['buttonTextColor'] . ' ; ';
    $stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$status]['borderColor'] . ' ; ';
    return $stylesEventStatus;
}

function bladeGetBankLogos()
{
    return config('constants.bankLogos');
}

function fixTimeToRemoveSeconds($time)
{
    if ($time == null) {
        return null;
    }
    if (substr_count($time, ':') === 2) {
        $time = explode(':', $time);
        $time = $time[0] . ':' . $time[1];
    }
    return $time;
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
        $stylesEventRatio .= "background-color: #EF4444; color: white;";
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
    $startTime= fixTimeToRemoveSeconds($startTime);
    if ($startDate != null && $startTime != null) {
        $carbonDateTimeUtc = Carbon::createFromFormat('Y-m-d H:i', $startDate . ' ' . $startTime, 'UTC') ?? null;
        // $carbonDateTimeUtc = $carbonDateTimeUtc->setTimezone('Asia/Singapore');
        $datePart = $carbonDateTimeUtc->format('Y-m-d');
        $timePart = $carbonDateTimeUtc->isoFormat('h:mm a');
        $dayStr = $carbonDateTimeUtc->englishDayOfWeek;
        $dateStr = $datePart . ' ' . $timePart;
        $combinedStr = $datePart . ' (' . $dayStr . ')';
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
        $eventBannerImg = asset('');
    }
    return $eventBannerImg;
}

function trustedBladeHandleImageFailure(){
    $imgFailure = asset('assets/images/broken-image.jpeg');
    return "onerror=\"this.onerror=null;this.src='$imgFailure';\"";    
}

function trustedBladeHandleImageFailureResize(){
    $imgFailure = asset('assets/images/broken-image.jpeg');
    return "onerror=\"this.onerror=null;this.width='500px';this.height='50px';this.src='$imgFailure';\"";    
}

function trustedBladeHandleImageFailureBanner(){
    $imgFailure = asset('assets/images/404.png');
    return "onerror=\"this.onerror=null;this.src='$imgFailure';\"";    
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

function bladeEventTowerLowerClass($eventTier)
{
    $eventTierLower= $eventTier ? strtolower($eventTier) : 'no-tier';
    return $eventTierLower;
}