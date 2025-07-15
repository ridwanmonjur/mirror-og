<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsWidget;
use Filament\Pages\Page;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.analytics';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Analytics Dashboard';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 100;

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'ADMIN';
    }
}