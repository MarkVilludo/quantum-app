<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject('Reset your Quantum Quizzes password')
                ->markdown('mail.password-reset', [
                    'name' => $notifiable->name ?? 'there',
                    'expireMinutes' => $expireMinutes,
                ])
                ->action('Choose a new password', $url);
        });
    }
}
