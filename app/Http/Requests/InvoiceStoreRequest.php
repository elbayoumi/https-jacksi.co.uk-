<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InvoiceUpdateRequest
 *
 * Validates input when updating an existing invoice.
 */
class InvoiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('seller')->check();
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'notes'     => ['nullable', 'string', 'max:1000'],
            'items'     => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'items.*.price'        => ['required', 'numeric', 'min:0'],
        ];
    }
}
