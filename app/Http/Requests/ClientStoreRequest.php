<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ClientStoreRequest
 *
 * Handles validation for creating new clients by sellers.
 * - Ensures each seller can’t register duplicate clients by email or phone.
 * - Provides clear error messages and strong validation rules.
 *
 * Expected payload:
 *  - name: string, required
 *  - email: string|email, optional but unique per seller
 *  - phone: string, optional but unique per seller
 *  - address: string, optional
 */
class ClientStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only authenticated sellers can create clients.
     */
    public function authorize(): bool
    {
        return auth('seller')->check();
    }

    /**
     * Define the validation rules.
     */
    public function rules(): array
    {
        $sellerId = auth('seller')->id();

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('clients')->where(fn($q) => $q->where('seller_id', $sellerId)),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('clients')->where(fn($q) => $q->where('seller_id', $sellerId)),
            ],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom error messages for better UX.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Client name is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email already exists in your clients list.',
            'phone.unique' => 'This phone number already exists in your clients list.',
        ];
    }
}
