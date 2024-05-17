<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\EventCategory;
use App\Models\EventDetail;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TotalEvent extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total User', User::count()),
            Card::make('Total Events', EventDetail::count()),
            Card::make('Total Event Category', EventCategory::count()),

        ];
    }
}
