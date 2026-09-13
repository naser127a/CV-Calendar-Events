<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:55'],
            'description' => ['nullable', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'all_day' => ['boolean'],
            'color' => ['nullable', 'string', 'max:20'],
            'status' => ['boolean'],
        ];
    }
}
