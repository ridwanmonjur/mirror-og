<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEventDetail extends CreateRecord
{
    protected static string $resource = EventDetailResource::class;
}
