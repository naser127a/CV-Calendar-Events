<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',
            'roleName' => ['sometimes', 'string', 'exists:roles,name'],
            'password' => 'required|min:8',

            'avatar' => ['nullable', 'image', 'mimes:jpg,png,gif,jpeg', 'max:2048'],
            'status' => "sometimes|boolean",

            'phone' => 'nullable|string|max:20',

        ];
    }
}
