<?php

namespace App\Filament\Resources\ParticipantFollowResource\Pages;

use App\Filament\Resources\ParticipantFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class ListParticipantFollows extends ListRecords
{
    protected static string $resource = ParticipantFollowResource::class;

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
