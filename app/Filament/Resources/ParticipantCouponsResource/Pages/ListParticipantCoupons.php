<?php

namespace App\Filament\Resources\ParticipantCouponsResource\Pages;

use App\Filament\Resources\ParticipantCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class ListParticipantCoupons extends ListRecords
{
    protected static string $resource = ParticipantCouponsResource::class;

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
