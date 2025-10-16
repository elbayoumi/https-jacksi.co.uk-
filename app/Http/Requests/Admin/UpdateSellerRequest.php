
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSellerRequest
 *
 * Handles validation for updating a seller record by an admin.
 * - Accepts name, email, and is_active fields.
 * - Used in SellerController@update.
 * - Automatically authorizes admin-guard users only.
 */
class UpdateSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * In this case, only authenticated admins can update sellers.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Notes:
     * - "sometimes" means the field is optional but will be validated if present.
     * - Email must be unique among all sellers except the one being updated.
     * - is_active must be a boolean (true/false).
     */
    public function rules(): array
    {
        $sellerId = $this->route('seller')?->id ?? 'NULL';

        return [
            'name'      => ['sometimes', 'string', 'max:255'],
            'email'     => ['sometimes', 'email', 'max:255', "unique:sellers,email,{$sellerId}"],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Custom validation messages for better readability.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered to another seller.',
            'email.email'  => 'Please enter a valid email address.',
            'name.string'  => 'The name must be a valid string.',
            'is_active.boolean' => 'The activation status must be true or false.',
        ];
    }
}
