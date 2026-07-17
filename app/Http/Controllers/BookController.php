<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

class BookController extends BaseController
{
    /**
     * Display a listing of all books
     */
    public function index(Request $request)
    {
        $query = Book::query();
        $search = $request->input('search', '');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        $books = $query->paginate(10);
        
        return Inertia::render('Books/Index', [
            'books' => $books,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new book
     */
    public function create()
    {
        return Inertia::render('Books/Create');
    }

    /**
     * Store a newly created book in database
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books|max:13',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('cover', 'public');
            $validated['cover_image'] = $path;
        }

        // Create the book
        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully!');
    }

    /**
     * Display a specific book
     */
    public function show(Book $book)
    {
        return Inertia::render('Books/Show', [
            'book' => $book,
        ]);
    }

    /**
     * Show the form for editing a book
     */
    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book,
        ]);
    }

    /**
     * Update a specific book
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:13|unique:books,isbn,' . $book->id,
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
        ]);

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Delete a book
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }

    public function __construct() 
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
}
