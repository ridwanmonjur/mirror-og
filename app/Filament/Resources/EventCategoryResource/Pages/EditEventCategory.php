<?php

namespace App\Filament\Resources\EventCategoryResource\Pages;

use App\Filament\Resources\EventCategoryResource;
use App\Models\EventCategory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventCategory extends EditRecord
{
    protected static string $resource = EventCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    EventCategory::clearCache();
                }),
        ];
    }

    protected function afterSave(): void
    {
        EventCategory::clearCache();
    }
}
