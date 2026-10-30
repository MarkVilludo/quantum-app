<x-mail::message>
# Confirm your email

Hi {{ $name }},

Thanks for joining **Quantum Quizzes**. Tap the button below to verify your email and unlock the mobile app and CMS.

<x-mail::button :url="$actionUrl">
Verify email address
</x-mail::button>

If you did not create an account, you can ignore this message.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
