<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email verified via signed link (no web session required — works from mobile mail clients).
     */
    public function __invoke(Request $request, string $id, string $verification_hash): RedirectResponse
    {
        if (! $request->hasValidRelativeSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        if (! hash_equals($verification_hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        $sessionUser = Auth::user();
        $sameSession = $sessionUser && (int) $sessionUser->getAuthIdentifier() === (int) $user->getKey();

        if ($user->hasVerifiedEmail()) {
            return $sameSession
                ? redirect()->route('dashboard')->with('status', 'email-already-verified')
                : redirect()->route('login')->with('status', 'email-already-verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $sameSession
            ? redirect()->route('dashboard')->with('status', 'email-verified')
            : redirect()->route('login')->with('status', 'email-verified');
    }
}
