<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
        'book_name' => 'nullable|string|max:255',
        'description' => 'nullable|string|min:10|max:5000',
        'language' => 'nullable|in:arabic,english,chinese,spanish,hindi,portuguese,russian,japanese,punjabi,german,javanese,korean,french,turkish,urdu,italian',
        'number_of_pages' => 'nullable|integer',
        'selling_price' => 'nullable|decimal:0,10',
        'rental_price' => 'nullable|decimal:0,10',
        'book_file' => 'nullable|file|mimes:pdf,epub|max:10240',
        'cover_image' => 'nullable|image|max:2048',
        'book_images' => 'nullable|array|min:1|max:5',
        'book_images.*' => 'image|max:2048',
        'author_id' => 'nullable|array',
        'author_id.*' => 'exists:authors,id',
        'category_id' => 'nullable|array',
        'category_id.*' => 'exists:categories,id',
        ];
    }
}
