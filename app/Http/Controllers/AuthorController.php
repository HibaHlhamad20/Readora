<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Models\Author;
use App\Services\ImageService;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function addAuthor (StoreAuthorRequest $request)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('author_image')) {
            $validatedData['author_image'] = ImageService::upload($request->file('author_image'),'authors');
        }

        $author = Author::create($validatedData);
        return response()->json(['message'=>'Author has been added successfully','data'=>$author], 201);
    }

    public function updateAuthor (Request $request,$id)
    {
        $author = Author::findOrFail($id);
        if ($request->filled('author_name'))
            {
                $author->author_name=$request->author_name;
            }
        if ($request->hasFile('author_image'))
            {
                ImageService::delete($author->author_image);
                $author->author_image=ImageService::upload($request->file('author_image'),'authors');
            }
        if ($request->filled('nationality'))
            {
                $author->nationality=$request->nationality;
            }
        if ($request->filled('description'))
            {
                $author->description=$request->description;
            }
        $author->save();

        return response()->json(['message'=>'Author has been updated successfully','data'=>$author], 200);
    }

    public function deleteAuthor($id)
    {
        $author = Author::findOrFail($id);
        ImageService::delete($author->author_image);
        $author->delete();

        return response()->json(['message'=>'Author has been deleted successfully','data'=>null], 200);
    }

    public function showAuthors()
    {
        $author = Author::all();

        return response()->json($author, 200);
    }


}
