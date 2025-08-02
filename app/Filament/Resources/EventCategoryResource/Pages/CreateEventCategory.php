<?php

namespace App\Filament\Resources\EventCategoryResource\Pages;

use App\Filament\Resources\EventCategoryResource;
use App\Models\EventCategory;
use Filament\Resources\Pages\CreateRecord;

class CreateEventCategory extends CreateRecord
{
    protected static string $resource = EventCategoryResource::class;

    protected function afterCreate(): void
    {
        EventCategory::clearCache();
    }
}
