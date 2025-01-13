<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->navigationGroups([
                // Primary operations
                NavigationGroup::make()
                    ->label('Event Results')
                    ->icon('heroicon-o-building')
                    ->items([
                        \App\Filament\Resources\EventDetailResource::class,
                        \App\Filament\Resources\AchievementsResource::class,
                        \App\Filament\Resources\AwardResultsResource::class,
                    ])
                    ->collapsible(false),

                // Financial section
                NavigationGroup::make()
                    ->label('Event Setup')
                    ->icon('heroicon-o-currency-dollar')
                    ->items([
                        \App\Filament\Resources\EventTierResource::class,
                        \App\Filament\Resources\EventCategoryResource::class,
                        \App\Filament\Resources\AchievementsResource::class,
                        \App\Filament\Resources\AwardResource::class,
                    ]),
                // Reporting
                NavigationGroup::make()
                    ->label('Profile')
                    ->icon('heroicon-o-chart-bar')
                    ->items([
                        \App\Filament\Resources\ActivityLogsResource::class,
                        \App\Filament\Resources\AddressResource::class,
                        \App\Filament\Resources\AwardResultsResource::class,
                    ]),
            ])
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#43A4D7'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
