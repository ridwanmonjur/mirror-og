<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Jobs\HandleEventUpdate;
use Filament\Actions\Action; // Changed this line
use App\Models\BracketDeadline;

class EditEventDetail extends EditRecord
{
    protected static string $resource = EventDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->after(function () {
                dispatch(new HandleEventUpdate($this->record));
                BracketDeadline::clearEventCache($this->record->id);
            });
    }
}
