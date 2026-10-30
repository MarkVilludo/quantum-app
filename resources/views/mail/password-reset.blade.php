<x-mail::message>
# Reset your password

Hello {{ $name }},

We received a request to reset the password for your **Quantum Quizzes** account. Use the button below to open the **CMS** and choose a new password (same site as the teacher/student web portal).

<x-mail::button :url="$actionUrl" color="primary">
{{ $actionText }}
</x-mail::button>

<x-mail::panel>
This link expires in **{{ $expireMinutes }} minutes**. If it expires, request a new reset from the sign-in screen.
</x-mail::panel>

If you did not request a password reset, you can safely ignore this email. Your password will not change.

Thanks,<br>
The Quantum Quizzes team

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste this URL into your web browser:",
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
</x-mail::message>
