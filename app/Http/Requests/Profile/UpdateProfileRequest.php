<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,png,gif,jpeg', 'max:2048'], // Max size 2MB
            '_method' => ['sometimes', 'string'],
        ];
    }

    // التحقق من وجود أي مفاتيح زائدة عن الـ rules ورفضها
    protected function prepareForValidation(): void
    {
        $allowedKeys = array_keys($this->rules());
        $extraKeys = array_diff(array_keys($this->all()), $allowedKeys);

        if (!empty($extraKeys)) {
            $errors = [];
            foreach ($extraKeys as $key) {
                $errors[$key] = ["The {$key} field is not allowed."];
            }

            throw ValidationException::withMessages($errors);
        }
    }
}
