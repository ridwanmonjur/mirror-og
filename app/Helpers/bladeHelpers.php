<?php

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
    $ratio = (float) $registeredParticipants / $totalParticipants;
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
