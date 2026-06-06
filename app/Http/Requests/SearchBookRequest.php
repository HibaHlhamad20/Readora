<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
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
        'book_name' => 'nullable|string|max:255',
        'language' => 'nullable|in:arabic,english,chinese,spanish,hindi,portuguese,russian,japanese,punjabi,german,javanese,korean,french,turkish,urdu,italian',
        'number_of_pages_from' => 'nullable|integer',
        'number_of_pages_to' => 'nullable|integer',
        'selling_price_from' => 'nullable|decimal:0,10',
        'selling_price_to' => 'nullable|decimal:0,10',
        'rental_price_from' => 'nullable|decimal:0,10',
        'rental_price_to' => 'nullable|decimal:0,10',
        'author_id' => 'nullable',
        'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
