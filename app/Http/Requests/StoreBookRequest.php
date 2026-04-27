<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
        'book_name' => 'required|string|max:255',
        'description' => 'required|string|min:10|max:5000',
        'language' => 'required|in:arabic,english,chinese,spanish,hindi,portuguese,russian,japanese,punjabi,german,javanese,korean,french,turkish,urdu,italian',
        'number_of_pages' => 'required|integer',
        'selling_price' => 'required|decimal:0,10',
        'rental_price' => 'required|decimal:0,10',
        'book_file' => 'required|file|mimes:pdf,epub|max:10240',
        'cover_image' => 'required|image|max:2048',
        'book_images' => 'required|array|min:1|max:5',
        'book_images.*' => 'image|max:2048',
        'author_ids' => 'required|array',
        'author_ids.*' => 'exists:authors,id',
        'category_ids' => 'required|array',
        'category_ids.*' => 'exists:categories,id',

        ];
    }
}
