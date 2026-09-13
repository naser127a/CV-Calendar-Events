<?php

namespace App\Http\Requests\News;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'type' => ['sometimes', 'in:news,announcement'],
            'published_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'boolean'],
            'is_breaking' => ['sometimes', 'boolean'],
            'breaking_until' => ['nullable', 'date'],
        ];
    }
}
