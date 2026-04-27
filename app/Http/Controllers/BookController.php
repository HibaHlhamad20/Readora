<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    
    public function addBook (StoreBookRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['cover_image'] = ImageService::upload($request->file('cover_image'),'books/covers');
        $validatedData['book_images'] = json_encode(ImageService::uploadMultiple($request->file('book_images'),'books/pages'));
        $validatedData['book_file'] = $request->file('book_file')->store('book_files','public');

        $book = Book::create(Arr::except($validatedData, ['author_ids','category_ids']));
        $book->authors()->sync($request->author_ids);
        $book->categories()->sync($request->category_ids); 
        return response()->json(['message'=>'Book has been added successfully','data'=>$book], 201);
    }

    public function updateBook (UpdateBookRequest $request,$id)
    {
        $book = Book::findOrFail($id);
        $validatedData = $request->validated();
        if ($request->hasFile('cover_image'))
            {
                ImageService::delete($book->cover_image);
                $validatedData['cover_image'] = ImageService::upload($request->file('cover_image'),'books/covers');
            }
        if ($request->hasFile('book_images'))
            {
                $images = json_decode($book->book_images, true) ?? [];
                foreach ($images as $img)
                    {
                        ImageService::delete($img);
                    }
                $validatedData['book_images'] = json_encode(ImageService::uploadMultiple($request->file('book_images'),'books/pages'));
            }
        if ($request->hasFile('book_file'))
            {
                Storage::disk('public')->delete($book->book_file);
                $validatedData['book_file'] = $request->file('book_file')->store('book_files','public');
            }

        $book->update(Arr::except($validatedData, ['author_ids','category_ids']));
        $book->refresh();

        if ($request->has('author_ids'))
            {
                $book->authors()->sync($request->author_ids);
            }
        if ($request->has('category_ids'))
            {
                $book->categories()->sync($request->category_ids);
            }
        
        
        return response()->json(['message'=>'Book has been updated successfully','data'=>$book], 200);
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);

        ImageService::delete($book->cover_image);
        $images = json_decode($book->book_images, true) ?? [];
        foreach ($images as $img)
            {
                ImageService::delete($img);
            }
        Storage::disk('public')->delete($book->book_file);
        $book->delete();

        return response()->json(['message'=>'Book has been deleted successfully','data'=>null], 200);
    }

    public function showBooks()
    {
        $books = Book::with(['authors','categories'])->orderBy('created_at','desc')->get();

        return response()->json($books, 200);
    }

    public function showBooksByRating()
    {
        $books = Book::with(['authors','categories'])->orderBy('rating','desc')->limit(30)->get();

        return response()->json($books, 200);
    }

    public function showNewBooks()
    {
        $books = Book::orderBy('created_at','desc')->limit(30)->get();

        return response()->json($books, 200);
    }

    public function showBooksByUserInterests()
    {
        $categoryIds = Auth::user()->categories->pluck('id');

        $books = Book::whereHas('categories',function ($query) use ($categoryIds)
        {
            $query->WhereIn ('categories.id',$categoryIds);
        })->limit(30)->get();

        return response()->json($books, 200);
    }



}
