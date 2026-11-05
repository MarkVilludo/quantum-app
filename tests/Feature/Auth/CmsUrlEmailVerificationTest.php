<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\CmsUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsUrlEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_link_uses_cms_url_and_passes_signature_check(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.cms_url' => 'https://quantumrealm.test',
        ]);
        CmsUrl::applyRoot();

        $user = User::factory()->unverified()->create();

        $url = CmsUrl::emailVerification(
            $user->id,
            sha1($user->getEmailForVerification()),
        );

        $this->assertStringStartsWith('https://quantumrealm.test/verify-email/', $url);
        $this->assertStringContainsString('signature=', $url);

        $this->get($url)->assertRedirect(route('login'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_relative_signature_validates_when_php_sees_http(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.cms_url' => 'https://quantumrealm.test',
        ]);
        CmsUrl::applyRoot();

        $user = User::factory()->unverified()->create();

        $url = CmsUrl::emailVerification(
            $user->id,
            sha1($user->getEmailForVerification()),
        );

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);

        $this->withServerVariables([
            'HTTP_HOST' => 'quantumrealm.test',
            'HTTPS' => 'off',
            'SERVER_PORT' => '80',
        ])->get($path.'?'.$query)->assertRedirect(route('login'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
