<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityLogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $perPage = $this->input('per_page');
        if ($perPage === null || $perPage === '') {
            $perPage = 15;
        }

        $this->merge([
            'per_page' => $perPage,
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'causer_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'event' => [
                'nullable',
                'string',
                'max:100',
            ],

            'subject_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'subject_id' => [
                'nullable',
                'integer',
            ],

            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],

            'sort_by' => [
                'nullable',
                Rule::in([
                    'id',
                    'description',
                    'event',
                    'subject_type',
                    'subject_id',
                    'causer_id',
                    'created_at',
                ]),
            ],

            'sort_dir' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],

            'per_page' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
