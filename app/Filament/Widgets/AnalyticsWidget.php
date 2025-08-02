<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AnalyticsWidget extends Widget
{
    protected static string $view = 'filament.widgets.analytics-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function getViewData(): array
    {
        return [
            'widgetId' => 'analytics-widget-'.uniqid(),
        ];
    }

    public static function canView(): bool
    {
        return false;
    }
}
