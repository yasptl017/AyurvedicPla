<?php

namespace App\Providers\Filament;

use App\Filament\Resources\MedicineResource;
use App\Livewire\TenantRegistration;
use App\Livewire\UserRegistration;
use App\Models\Clinic;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use App\Filament\App\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->resources([
                MedicineResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([
            ])
            ->navigationItems([
                NavigationItem::make('Admin')
                    ->url('/admin')
                    ->icon('heroicon-o-shield-check')
                    ->openUrlInNewTab()
                    ->group('Management')
                    ->sort(3),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth(Width::TwoExtraLarge->value)
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
            ->maxContentWidth(Width::Full)
            ->spa()
            ->profile()
            ->login()
            ->registration(UserRegistration::class)
            ->tenantRegistration(TenantRegistration::class)
            ->authMiddleware([
                Authenticate::class,
            ])->tenant(Clinic::class, 'ClinicUrl')->tenantMenuItems([
                'register' => MenuItem::make()->label('Register new team')->visible(false),
            ]);
    }
}
