<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\CmsUrl;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = CmsUrl::emailVerification(
            $user->id,
            sha1($user->getEmailForVerification()),
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('login'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail(), 'Response status: '.$response->status());
    }

    public function test_logged_in_user_can_verify_via_email_link(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = CmsUrl::emailVerification(
            $user->id,
            sha1($user->getEmailForVerification()),
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        config(['app.cms_url' => 'http://127.0.0.1:8000']);
        CmsUrl::applyRoot();

        $verificationUrl = CmsUrl::emailVerification(
            $user->id,
            sha1('wrong-email'),
        );

        $response = $this->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }
}
