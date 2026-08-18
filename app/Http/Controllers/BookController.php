<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
       // return response()->json(['message'=>'Book has been added successfully','data'=>$book], 201);

       if ($request->has('questions')) {
        foreach ($request->questions as $qData) {
            $book->questions()->create([
                'question_text' => $qData['question_text'],
                'options' => $qData['options'], 
                'correct_answer' => $qData['correct_answer'], 
            ]);
            }
        }
        return response()->json(['message'=>'Book has been added successfully',
        'data'=>$book->load('questions')],
         201);
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
        $books = Book::with(['authors','categories'])
        ->where('rating', '>', 0)//ضفت هي 
        ->orderBy('rating','desc')
        ->limit(30)->get();

        return response()->json($books, 200);
    }

    // public function showNewBooks()
    // {
    //     $books = Book::orderBy('created_at','desc')->limit(30)->get();

    //     return response()->json($books, 200);
    // }
    //عدلت لحتى يطلع اسم المؤلف
public function showNewBooks()
{
    $books = Book::with(['authors','categories'])
                 ->orderBy('created_at','desc')
                 ->limit(30)
                 ->get();

    return response()->json($books, 200);
}
    // public function showBooksByUserInterests()
    // {
    //     $categoryIds = Auth::user()->categories->pluck('id');

    //     $books = Book::whereHas('categories',function ($query) use ($categoryIds)
    //     {
    //         $query->WhereIn ('categories.id',$categoryIds);
    //     })->limit(30)->get();

    //     return response()->json($books, 200);
    // }
//ضفت اسم الكؤلف
    public function showBooksByUserInterests()
{
    $categoryIds = Auth::user()->categories->pluck('id');

    $books = Book::with(['authors', 'categories']) //   
        ->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
        ->limit(30)
        ->get();

    return response()->json($books, 200);
}

    public function showBookDetails ($id)
    {
        $book=Book::findOrFail($id);
        return response()->json($book, 200);
    }

    public function searchBooks (SearchBookRequest $request)
    {
        $book = Book::with('authors','categories');

        if ($request->filled('book_name'))
            $book->where('book_name', 'LIKE', '%'.$request->book_name.'%');

        if ($request->filled('language'))
            $book->where('language', $request->language);

        if ($request->filled('number_of_pages_from'))
            $book->where('number_of_pages', '>=', $request->number_of_pages_from);

        if ($request->filled('number_of_pages_to'))
            $book->where('number_of_pages', '<=', $request->number_of_pages_to);

        if ($request->filled('selling_price_from'))
            $book->where('selling_price', '>=', $request->selling_price_from);

        if ($request->filled('selling_price_to'))
            $book->where('selling_price', '<=', $request->selling_price_to);

        if ($request->filled('rental_price_from'))
            $book->where('rental_price', '>=', $request->rental_price_from);

        if ($request->filled('rental_price_to'))
            $book->where('rental_price', '<=', $request->rental_price_to);

      
        
    if ($request->filled('author_id')) {
        $book->whereHas('authors', function ($query) use ($request) {
            $query->where('authors.id', $request->author_id);
        });        
    } 
    //   الفلترة باسم المؤلف إذا أُرسل الاسم كامل 
    elseif ($request->filled('author_name')) {
        $book->whereHas('authors', function ($query) use ($request) {
            $query->where('author_name', 'LIKE', '%'.$request->author_name.'%');
        });
    }
        if ($request->filled('category_id')) {
            $book->whereHas('categories',function ($query) use ($request) {
                $query->where('category_id',$request->category_id);
            });
        }

        $books = $book->get();

        return response()->json($books, 200);
    }

    public function searchAuthors (Request $request)
    {
        $validate = $request->validate([
            'author_name' => 'required|string|min:1',
        ]);
        $authors = Author::where('author_name', 'LIKE', '%'.$validate['author_name'].'%')->select('id','author_name')->get();

        return response()->json($authors, 200);
    }

    public function addToFavourite ($id)
    {
        $book=Book::findOrFail($id);
        Auth::user()->favouriteBooks()->syncWithoutDetaching($id);
        return response()->json(['message'=>'Book added to favourite successfully','data'=>$book], 200);
    }

    public function removeFromFavourite ($id)
    {
        Auth::user()->favouriteBooks()->detach($id);
        return response()->json(['message'=>'Book removed from favourite successfully','data'=>null], 200);
    }

    public function showFavourites ()
    {
        $books=Auth::user()->favouriteBooks()->get();
        return response()->json($books, 200);
    }

    public function addRating(Request $request, $id)
    {
        $book=Book::findOrFail($id);
        $validate=$request->validate([
            'rating' => 'required|min:1|max:5|integer'
        ]);

        $old_rating = DB::table('ratings')->where('user_id',Auth::id())->where('book_id',$book->id) 
        ->first();

        if ($old_rating) {
            return response()->json(['message'=> 'User has rated already.'],409);
        }

        DB::table('ratings')->insert([
            'user_id'=>Auth::id(),
            'book_id'=>$book->id,
            'rating'=>$validate['rating'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $book->rating_count = $book->rating_count + 1;
        $book->rating_sum = $book->rating_sum + $validate['rating'];
        $book->rating = $book->rating_sum / $book->rating_count;
        $book->save();

        return response()->json(['message'=>'Rating added successfully','data'=>[
            'book_id'=>$book->id, 'rating'=>$validate['rating']
        ]], 201);
    }

    public function updateRating(Request $request, $id)
    {
        $book=Book::findOrFail($id);
        $validate=$request->validate([
            'rating' => 'required|min:1|max:5|integer'
        ]);

        $old_rating = DB::table('ratings')->where('user_id',Auth::id())->where('book_id',$book->id) 
        ->first();

        if (! $old_rating) {
            return response()->json(['message'=> 'User has not rated yet.'],404);
        }

        DB::table('ratings')->where('id',$old_rating->id)->update([
            'rating'=>$validate['rating'],
            'updated_at' => now(),
        ]);

        $book->rating_sum = $book->rating_sum - $old_rating->rating + $validate['rating'];
        $book->rating = $book->rating_sum / $book->rating_count;
        $book->save();

        return response()->json(['message'=>'Rating updated successfully','data'=>[
            'book_id'=>$book->id, 'rating'=>$validate['rating']
        ]], 200);
    }
    

}
