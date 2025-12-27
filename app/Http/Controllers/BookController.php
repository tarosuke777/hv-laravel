<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedTitle = $request->input('title');

        $query = Book::with('pages'); // Eager Loadで最初の1枚（表紙）などを取得しやすくする

        if ($request->title) {
           $query->where('title', $request->title);
        }

        $books = $query->paginate(12);
        $uniqueTitles = Book::pluck('title')->unique();

        return view('books.index', compact('books', 'selectedTitle','uniqueTitles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);
        $pages = $book->pages()->orderBy('page_number')->get();
        return view('books.show', compact('book', 'pages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
