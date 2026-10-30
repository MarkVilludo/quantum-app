<?php

namespace App\Providers;

use App\Support\CmsUrl;
use App\Support\HostingerPaths;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        HostingerPaths::useSiblingPublicHtmlIfPresent($this->app);

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            \DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureViteForMobileEmulator();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return CmsUrl::passwordReset($token, $notifiable->getEmailForPasswordReset());
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = CmsUrl::passwordReset($token, $notifiable->getEmailForPasswordReset());

            $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject('Reset your Quantum Quizzes password')
                ->markdown('mail.password-reset', [
                    'name' => $notifiable->name ?? 'there',
                    'expireMinutes' => $expireMinutes,
                ])
                ->action('Choose a new password', $url);
        });

        VerifyEmail::createUrlUsing(function (object $notifiable) {
            return CmsUrl::emailVerification(
                $notifiable->getKey(),
                sha1($notifiable->getEmailForVerification()),
            );
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify your Quantum Quizzes email')
                ->markdown('mail.verify-email', [
                    'name' => $notifiable->name ?? 'there',
                ])
                ->action('Verify email address', $url);
        });
    }

    /**
     * The Android emulator cannot load Vite's dev server from public/hot ([::1]:5173).
     * Use compiled public/build assets for 10.0.2.2 / 10.0.3.2 requests even when npm run dev is active.
     */
    private function configureViteForMobileEmulator(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $host = request()->getHost();

        if (in_array($host, ['10.0.2.2', '10.0.3.2'], true)) {
            Vite::useHotFile(public_path('hot-emulator-skip'));
        }
    }
}
