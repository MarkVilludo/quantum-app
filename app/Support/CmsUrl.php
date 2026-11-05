<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;

class CmsUrl
{
    public static function base(): string
    {
        return rtrim((string) config('app.cms_url', config('app.url')), '/');
    }

    /** Force signed links and mail URLs to use {@see base()}. */
    public static function applyRoot(): void
    {
        $root = self::base();
        URL::forceRootUrl($root);

        if (str_starts_with($root, 'https://')) {
            URL::forceScheme('https');
        }
    }

    public static function passwordReset(string $token, string $email): string
    {
        return self::base().'/reset-password/'.$token.'?'.http_build_query([
            'email' => $email,
        ]);
    }

    public static function forgotPasswordPage(): string
    {
        return self::base().'/forgot-password';
    }

    public static function emailVerification(int|string $userId, string $hash, int $expiresMinutes = 60): string
    {
        self::applyRoot();

        $relative = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($expiresMinutes),
            [
                'id' => $userId,
                'verification_hash' => $hash,
            ],
            absolute: false,
        );

        return self::base().$relative;
    }
}
