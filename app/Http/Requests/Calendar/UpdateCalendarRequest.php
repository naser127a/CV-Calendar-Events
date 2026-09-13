<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:55'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['sometimes', 'nullable', 'date'],
            'all_day' => ['sometimes', 'boolean'],
            'color' => ['sometimes', 'nullable', 'string', 'max:20'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
