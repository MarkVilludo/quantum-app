<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAccountRequest;
use App\Support\CmsUrl;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthController extends Controller
{
    public function register(RegisterAccountRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'student',
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'Account created. We sent a verification link to your email. Verify it before signing in.',
            'requires_email_verification' => true,
            'email' => $user->email,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => User::normalizeEmail($request->input('email', '')),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email address before signing in. Check your inbox for the verification link.',
                'email_verification_required' => true,
                'email' => $user->email,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->merge([
            'email' => User::normalizeEmail($request->input('email', '')),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::findByEmail($request->email);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'This email is already verified. You can sign in.',
                'email_verified' => true,
            ]);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (TransportExceptionInterface $e) {
            throw ValidationException::withMessages([
                'email' => ['Could not send email. Start MailHog (port 1025) or check mail settings, then try again.'],
            ]);
        }

        return response()->json([
            'message' => 'We sent a new verification link to your email.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function forgotPassword(Request $request)
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

        try {
            $status = Password::sendResetLink([
                'email' => $user->email,
            ]);
        } catch (TransportExceptionInterface $e) {
            throw ValidationException::withMessages([
                'email' => ['Could not send email. Start MailHog (port 1025) or check mail settings, then try again.'],
            ]);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'We sent a password reset link to that email. Open it in your browser to choose a new password.',
                'cms_url' => CmsUrl::base(),
                'forgot_password_url' => CmsUrl::forgotPasswordPage(),
            ]);
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
