<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\EventDetail;
use App\Models\EventCategory;
use App\Models\User;

class TotalEvent extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total User', User::count()),
            Card::make('Total Events', EventDetail::count()),
            Card::make('Total Event Category', EventCategory::count() ),

        ];
    }
}
