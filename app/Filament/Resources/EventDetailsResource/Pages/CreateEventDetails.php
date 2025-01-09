<?php

namespace App\Filament\Resources\EventDetailsResource\Pages;

use App\Filament\Resources\EventDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEventDetails extends CreateRecord
{
    protected static string $resource = EventDetailsResource::class;
}
