<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClientUpdateRequest
 *
 * Handles validation and authorization for updating an existing client.
 * This FormRequest ensures data integrity before passing it to the controller or repository layer.
 */
class ClientUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * We assume the route model binding already ensures ownership
     * via controller authorization checks, so we allow the request itself.
     */
    public function authorize(): bool
    {
        // You may optionally check guard here if needed:
        // return auth('seller')->check();
        return true;
    }

    /**
     * Validation rules applied when updating a client.
     *
     * - The email must be unique per seller.
     * - Phone must be valid and within typical length constraints.
     */
    public function rules(): array
    {
        $clientId = $this->route('client')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                // Ignore the current record's ID when checking uniqueness
                "unique:clients,email,{$clientId},id",
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'Client name is required.',
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'This email is already registered for another client.',
            'phone.max'      => 'Phone number is too long.',
        ];
    }
}
