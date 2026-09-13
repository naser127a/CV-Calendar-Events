<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class CalendarFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->has('status')
                ? filter_var($this->status, FILTER_VALIDATE_BOOLEAN)
                : null,

            'per_page' => $this->per_page
                ? (int) $this->per_page
                : 15,
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],

            'sort_by' => ['nullable', 'in:id,title,start_at,end_at,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],

            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
