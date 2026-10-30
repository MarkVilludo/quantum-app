<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email verified via signed link (no web session required — works from mobile mail clients).
     */
    public function __invoke(Request $request, string $id, string $verification_hash): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        if (! hash_equals($verification_hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'email-already-verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('login')->with('status', 'email-verified');
    }
}
