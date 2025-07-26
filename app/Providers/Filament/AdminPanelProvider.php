<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
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
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder

                    ->item(
                        NavigationItem::make('Dashboard')
                            ->icon('heroicon-o-home')
                            ->activeIcon('heroicon-s-home')
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                            ->url(fn (): string => Dashboard::getUrl())
                    )
                    ->groups([
                        NavigationGroup::make('Users & Teams')
                        ->items([
                            ...\App\Filament\Resources\UserResource::getNavigationItems(),
                            // ...\App\Filament\Resources\ActivityLogsResource::getNavigationItems(),
                            ...\App\Filament\Resources\TransactionHistoryResource::getNavigationItems(),
                            ...\App\Filament\Resources\InterestedUserResource::getNavigationItems(),
                            ...\App\Filament\Resources\WithdrawalPasswordResource::getNavigationItems(),
                            ...\App\Filament\Resources\TeamResource::getNavigationItems(),
                        ]),
                        NavigationGroup::make('Social')
                            ->items([
                                ...\App\Filament\Resources\BlocksResource::getNavigationItems(),
                                ...\App\Filament\Resources\ReportResource::getNavigationItems(),
                                ...\App\Filament\Resources\FriendResource::getNavigationItems(),
                                ...\App\Filament\Resources\LikeResource::getNavigationItems(),
                                ...\App\Filament\Resources\OrganizerFollowResource::getNavigationItems(),
                                ...\App\Filament\Resources\ParticipantFollowResource::getNavigationItems(),
                        ]),
                        NavigationGroup::make('Event Details')
                            ->items([
                                ...\App\Filament\Resources\EventDetailResource::getNavigationItems(),
                                ...\App\Filament\Resources\JoinEventResource::getNavigationItems(),
                                ...\App\Filament\Resources\PaymentIntentResource::getNavigationItems(),
                            ])
                            ->collapsible(false),
                        
         
                        NavigationGroup::make('Setup')
                            ->items([
                                ...\App\Filament\Resources\EventTypeResource::getNavigationItems(),
                                ...\App\Filament\Resources\EventTierResource::getNavigationItems(),
                                ...\App\Filament\Resources\EventCategoryResource::getNavigationItems(),
                                ...\App\Filament\Resources\SystemCouponsResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Shop')
                            ->items([
                                ...\App\Filament\Resources\ProductResource::getNavigationItems(),
                                ...\App\Filament\Resources\CategoryResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Analytics')
                            ->items([
                                NavigationItem::make('Analytics')
                                    ->icon('heroicon-o-chart-bar')
                                    ->activeIcon('heroicon-s-chart-bar')
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.analytics'))
                                    ->url(fn (): string => \App\Filament\Pages\Analytics::getUrl())
                                    ->visible(fn (): bool => auth()->check() && auth()->user()->role === 'ADMIN'),
                            ]),
         
                   
                        
                    ]);
            })
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
