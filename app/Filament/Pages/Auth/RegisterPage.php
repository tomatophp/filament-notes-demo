<?php

namespace App\Filament\Pages\Auth;


use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use TomatoPHP\FilamentAccounts\Events\SendOTP;
use TomatoPHP\FilamentAccounts\Filament\Pages\Auth\RegisterAccount;
use TomatoPHP\FilamentAccounts\Responses\RegisterResponse;

class RegisterPage extends RegisterAccount
{
    protected static string $view = 'auth.register';

    public function register(): ?RegisterResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $user = DB::transaction(function () {
            $data = $this->form->getState();

            return $this->getUserModel()::create($data);
        });

        event(new Registered($user));

        $user->otp_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        $user->save();

        Notification::make()
            ->title('New Notes Demo User')
            ->body(collect([
                'NAME: '.$user->name,
                'EMAIL: '.$user->email,
                'USERNAME: '.$user->username,
                'OTP: '.$user->otp_code,
                'DATE: '.Carbon::now()->diffForHumans(),
                'URL: '.url('/'),
            ])->implode("\n"))
            ->sendToDiscord();

        event(new SendOTP(config('filament-accounts.model'), $user->id));
        return app(RegisterResponse::class);
    }
}
