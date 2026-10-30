<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidRegistrationName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => User::normalizeEmail($this->input('email', '')),
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new ValidRegistrationName()],
            'year_id' => ['nullable', 'exists:years,id'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter your name.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Passwords do not match.',
        ];
    }
}
