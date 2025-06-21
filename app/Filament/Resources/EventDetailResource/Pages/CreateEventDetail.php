<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use App\Jobs\HandleEventUpdate;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEventDetail extends CreateRecord
{
    protected static string $resource = EventDetailResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->after(function () {
                dispatch(new HandleEventUpdate($this->record));
            });
    }
}
