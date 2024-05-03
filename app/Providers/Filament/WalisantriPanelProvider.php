<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\WSDashboard;
use App\Filament\Walisantri\Resources\DataSantriResource\Widgets\FormulirKedatangan;
use App\Filament\Walisantri\Resources\DataSantriResource\Widgets\Santri;
use Filament\Notifications\Livewire\Notifications;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class WalisantriPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('walisantri')
            ->path('walisantri')
            ->colors([
                'danger' => "#9e5d4b",
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => "#d3c281",
                'success' => "#274043",
                'warning' => Color::Orange,
                'white' => "#FFFFFF",
            ])
            ->font('Raleway')
            ->brandLogo(asset('SiakadTSN V1 Logo.png'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('favicon-32x32.png'))
            ->discoverResources(in: app_path('Filament/Walisantri/Resources'), for: 'App\\Filament\\Walisantri\\Resources')
            ->discoverWidgets(in: app_path('Filament/Walisantri/Widgets'), for: 'App\\Filament\\Walisantri\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Santri::class,
                FormulirKedatangan::class,
            ])
            ->discoverPages(in: app_path('Filament/Walisantri/Pages'), for: 'App\\Filament\\Walisantri\\Pages')
            ->pages([
                WSDashboard::class,
            ])
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
            ])
            ->unsavedChangesAlerts()
            ->defaultThemeMode(ThemeMode::Light)
            ->topNavigation()
            ->maxContentWidth('full')
            ->bootUsing(function () {
                Notifications::alignment(Alignment::Center);
                Notifications::verticalAlignment(VerticalAlignment::Center);
            })
            ->breadcrumbs(false);
        // ->navigationGroups([
        //     NavigationGroup::make()
        //          ->label('PSB')
        //          ->icon('heroicon-s-user-group'),
        //     NavigationGroup::make()
        //         ->label('Imtihan')
        //         ->icon('heroicon-s-inbox-stack'),
        // ]);
    }
}
