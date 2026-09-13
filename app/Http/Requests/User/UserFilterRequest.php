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
        $this->merge([
            'status'   => $this->has('status')
                ? filter_var($this->status, FILTER_VALIDATE_BOOLEAN)
                : null,

            'roleName'  => $this->roleName
                ? (string) $this->roleName
                : null,

            'per_page' => $this->per_page
                ? (int) $this->per_page
                : 15,
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

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
