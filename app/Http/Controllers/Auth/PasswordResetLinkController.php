<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'mailDevInboxUrl' => self::mailDevInboxUrl(),
        ]);
    }

    private static function mailDevInboxUrl(): ?string
    {
        if (! config('app.debug')) {
            return null;
        }

        $host = request()->getHost();

        return 'http://'.$host.':8025';
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => User::normalizeEmail($request->input('email', '')),
        ]);

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::findByEmail($request->email);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => [__('passwords.user')],
            ]);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        if ($status === Password::INVALID_USER) {
            throw ValidationException::withMessages([
                'email' => [__('passwords.user')],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
