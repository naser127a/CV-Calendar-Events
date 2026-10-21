<?php

namespace App\Http\Requests\News;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $perPage = $this->input('per_page');
        if ($perPage === null || $perPage === '') {
            $perPage = 10;
        }

        $this->merge(['per_page' => $perPage]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:news,announcement'],
            'source_type' => ['nullable', 'in:local,external'],
            'status' => ['nullable', 'boolean'],
            'is_breaking' => ['nullable', 'boolean'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'published_from' => ['nullable', 'date'],
            'published_to' => ['nullable', 'date', 'after_or_equal:published_from'],
            'sort_by' => [
                'nullable',
                Rule::in(['id', 'title', 'type', 'source_type', 'published_at', 'created_at']),
            ],
            'sort_order' => ['nullable', 'in:asc,desc'],
            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
