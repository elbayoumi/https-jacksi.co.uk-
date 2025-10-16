<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ClientIndexRequest
 *
 * Validates and sanitizes query string parameters for the client index endpoint.
 * This ensures we only accept a safe subset of parameters (search, pagination, sorting),
 * preventing unexpected inputs from affecting queries or UI.
 */
class ClientIndexRequest extends FormRequest
{
    /**
     * Authorize the request for authenticated sellers only.
     */
    public function authorize(): bool
    {
        return auth('seller')->check();
    }

    /**
     * Sanitize incoming data before validation.
     * - Trim whitespace.
     * - Normalize casing for sorting direction.
     * - Provide sensible defaults when missing.
     */
    protected function prepareForValidation(): void
    {
        $search = $this->query('search');

        $this->merge([
            'search'   => is_string($search) ? trim(preg_replace('/\s+/', ' ', $search)) : null,
            'order'    => strtolower((string) $this->query('order', 'asc')),
            'sort'     => $this->query('sort', 'name'),
            'page'     => (int) $this->query('page', 1),
            'per_page' => (int) $this->query('per_page', 15),
        ]);
    }

    /**
     * Validation rules for query parameters.
     *
     * Notes:
     * - We cap search length and restrict characters loosely (letters, numbers, spaces, basic symbols).
     * - Pagination is bounded to a safe set for performance.
     * - Sorting fields are whitelisted to prevent SQL injection/unknown columns.
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:100',
                // Optional soft restraint; allows letters, numbers, spaces, and a few punctuation marks
                'regex:/^[\pL\pM\pN\s\-\.\,_@+()]*$/u',
            ],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50'],
            'sort' => ['nullable', 'in:name,email,created_at'],
            'order' => ['nullable', 'in:asc,desc'],
        ];
    }

    /**
     * Custom messages (optional but helpful).
     */
    public function messages(): array
    {
        return [
            'search.regex' => 'The search term contains unsupported characters.',
        ];
    }

    /**
     * Provide conveniently typed, safe accessors.
     */
    public function search(): ?string
    {
        /** @var string|null $s */
        $s = $this->validated('search');
        return $s ? $this->escapeLikeWildcards($s) : null;
    }

    public function sort(): string
    {
        return (string) $this->validated('sort', 'name');
    }

    public function order(): string
    {
        return (string) $this->validated('order', 'asc');
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 15);
    }

    /**
     * Escape LIKE wildcards (% and _) to avoid unintended pattern matching.
     */
    protected function escapeLikeWildcards(string $term): string
    {
        // Replace % and _ with escaped versions to treat them as literals
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term);
    }
}
