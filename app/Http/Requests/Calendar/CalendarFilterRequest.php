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
        $status = $this->input('status');
        if ($status === '') {
            $status = null;
        } elseif (is_string($status) || is_int($status) || is_bool($status)) {
            $normalizedStatus = filter_var(
                $status,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($normalizedStatus !== null) {
                $status = $normalizedStatus;
            }
        }

        $perPage = $this->input('per_page');
        if ($perPage === null || $perPage === '') {
            $perPage = 15;
        }

        $this->merge([
            'status' => $status,
            'per_page' => $perPage,
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

            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
