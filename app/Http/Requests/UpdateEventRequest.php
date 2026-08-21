<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'event_name' => 'nullable|string|max:200',
            'start_date' => 'nullable|date|after_or_equal:now',
            'end_date' => 'nullable|date',
            'book_ids' => 'nullable|array|min:1',
            'book_ids.*' => 'exists:books,id',
            'points' => 'nullable|integer'
        ];
    }
}
