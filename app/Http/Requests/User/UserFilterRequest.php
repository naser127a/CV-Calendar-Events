<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
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

        $status = $this->input('status');
        if ($this->has('status') && (is_string($status) || is_int($status) || is_bool($status))) {
            $normalizedStatus = filter_var(
                $status,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($normalizedStatus !== null) {
                $status = $normalizedStatus;
            }
        }

        $this->merge([
            'status' => $status,
            'roleName' => $this->filled('roleName')
                ? (string) $this->input('roleName')
                : null,
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

            'status' => [
                'nullable',
                'boolean',
            ],

            'roleName' => [
                'nullable',
                'string',
                'exists:roles,name',
            ],

            'sort_by' => [
                'nullable',
                'in:id,name,email,created_at,last_login',
            ],

            'sort_dir' => [
                'nullable',
                'in:asc,desc',
            ],

            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
