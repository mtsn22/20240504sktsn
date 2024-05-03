<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource\Widgets\ListPendaftarSantriBaru;
use App\Filament\Tsn\Widgets\ListPendaftarSantriBaruAll;
use App\Filament\Tsn\Widgets\ListPendaftar;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Livewire\Notifications;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Widgets;
use Filament\Widgets\Widget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TsnPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tsn')
            ->path('tsn')
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
            ->discoverResources(in: app_path('Filament/Tsn/Resources'), for: 'App\\Filament\\Tsn\\Resources')
            ->discoverWidgets(in: app_path('Filament/Tsn/Widgets'), for: 'App\\Filament\\Tsn\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->discoverPages(in: app_path('Filament/Tsn/Pages'), for: 'App\\Filament\\Tsn\\Pages')
            ->pages([
                Dashboard::class,
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
            ->breadcrumbs(false)
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s');
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
