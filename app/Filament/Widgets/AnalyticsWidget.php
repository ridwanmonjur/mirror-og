<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
