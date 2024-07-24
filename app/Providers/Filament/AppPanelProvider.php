<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\LoginPage;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentAccounts\Filament\Pages\Auth\LoginAccount;
use TomatoPHP\FilamentAccounts\FilamentAccountsSaaSPlugin;
use TomatoPHP\FilamentNotes\Filament\Widgets\NotesWidget;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsSwitcherPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('favicon.ico'))
            ->brandLogo(asset('tomato.png'))
            ->brandLogoHeight('80px')
            ->font(
                'IBM Plex Sans Arabic',
                provider: GoogleFontProvider::class,
            )
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                NotesWidget::class
            ])
            ->plugin(
                FilamentAccountsSaaSPlugin::make()
                    ->databaseNotifications()
                    ->checkAccountStatusInLogin()
                    ->APITokenManager()
                    ->editTeam()
                    ->deleteTeam()
                    ->teamInvitation()
                    ->showTeamMembers()
                    ->editProfile()
                    ->editPassword()
                    ->browserSesstionManager()
                    ->deleteAccount()
                    ->editProfileMenu()
                    ->registration()
                    ->useOTPActivation(),
            )
            ->plugin(
                FilamentNotesPlugin::make()
                    ->useShareLink()
                    ->useCheckList()
                    ->useUserAccess()
                    ->useGroups()
                    ->useStatus()
            )
            ->plugin(FilamentTranslationsSwitcherPlugin::make())
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
            ->login(LoginPage::class);
    }
}
