<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRegistrationName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $name = trim($value);

        if ($name === '') {
            return;
        }

        if (preg_match('/\d/', $name)) {
            $fail('Name cannot contain numbers.');

            return;
        }

        if (! preg_match('/^[\p{L}\s]+$/u', $name)) {
            $fail('Name cannot contain numbers or special characters.');
        }
    }
}
