<?php

namespace App\Support;

class CmsUrl
{
    public static function base(): string
    {
        return rtrim((string) config('app.cms_url', config('app.url')), '/');
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
        $relative = \Illuminate\Support\Facades\URL::temporarySignedRoute(
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
