<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DashboardIndexRequest
 *
 * Handles validation for seller dashboard filters.
 * This request validates optional query parameters
 * like date ranges or pagination filters passed to the dashboard.
 *
 * Example accepted query:
 *   /seller/dashboard?from=2025-10-01&to=2025-10-15
 */
class DashboardIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Restricts access to authenticated sellers only.
     */
    public function authorize(): bool
    {
        return auth('seller')->check();
    }

    /**
     * Define validation rules for dashboard filters.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date', 'before_or_equal:to'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }

    /**
     * Customize validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from.date' => 'The "from" date must be a valid date (YYYY-MM-DD).',
            'to.date'   => 'The "to" date must be a valid date (YYYY-MM-DD).',
            'from.before_or_equal' => 'The start date cannot be after the end date.',
            'to.after_or_equal'    => 'The end date cannot be before the start date.',
        ];
    }

    /**
     * Prepare the data before validation.
     *
     * Here we normalize the inputs or trim unwanted characters.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('from')) {
            $this->merge(['from' => trim($this->input('from'))]);
        }
        if ($this->filled('to')) {
            $this->merge(['to' => trim($this->input('to'))]);
        }
    }
}
