<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreSellerRequest
 *
 * Handles validation for creating a new Seller by an admin.
 * - Ensures clean data structure and strong password requirements.
 * - Guards against duplicate emails.
 * - Uses human-readable validation messages.
 *
 * Notes:
 * - Password is required (minimum 8 chars by default, adjustable).
 * - Email must be unique in the "sellers" table.
 * - The "is_active" field is optional (defaults to true if omitted).
 */
class StoreSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Only authenticated admins should access this route.
     * Since the controller is already protected by 'auth:admin',
     * we can safely return true here.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Define validation rules for storing a new seller.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:sellers,email'],
            'password'   => ['required', 'string', 'min:8'], // Adjust to use Password::defaults() if needed
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Customize validation messages for better UX.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'The seller name is required.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email is already in use by another seller.',
            'password.required'  => 'A password must be provided.',
            'password.min'       => 'The password must be at least 8 characters long.',
            'is_active.boolean'  => 'The "active" field must be true or false.',
        ];
    }

    /**
     * Prepare incoming data before validation.
     *
     * This allows us to normalize boolean flags or trim strings.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true), // default to true if not set
            'email'     => trim((string) $this->input('email')),
            'name'      => trim((string) $this->input('name')),
        ]);
    }
}
