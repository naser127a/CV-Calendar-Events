<?php

namespace App\Http\Requests\News;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewsFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'type' => ['nullable', 'in:news,announcement'],
            'source_type' => ['nullable', 'in:local,external'],
            'status' => ['nullable', 'boolean'],
            'is_breaking' => ['nullable', 'boolean'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'published_from' => ['nullable', 'date'],
            'published_to' => ['nullable', 'date'],
            'sort_by' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
