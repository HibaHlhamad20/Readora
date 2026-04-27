<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [     
            'author_name' => 'required|string|max:255',
            'author_image' => 'nullable|image|max:2048',
            'nationality' => 'nullable|string',
            'description' => 'nullable|string|min:10|max:5000',
        ];
    }
}
