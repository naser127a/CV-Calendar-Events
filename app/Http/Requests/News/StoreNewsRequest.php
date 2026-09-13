<?php

namespace App\Http\Requests\News;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'type' => ['required', 'in:news,announcement'],
            'source_type' => ['required', 'in:local,external'],
            'external_id' => [
                'nullable',
                'string',
                'unique:news,external_id',
                'required_if:source_type,external'
            ],
            'external_url' => [

                'nullable',

                'required_if:source_type,external',

                'url'

            ],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
            'is_breaking' => ['required', 'boolean'],
            'breaking_until' => ['nullable', 'date'],
        ];
    }
}
