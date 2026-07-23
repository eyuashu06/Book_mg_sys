<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // Filter by reading status
        if ($request->has('status') && $request->status) {
            $query->where('reading_status', $request->status);
        }

        $books = $query->paginate(12);
        
        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => $request->only(['search', 'status'])
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
            'isbn' => 'nullable|string|unique:books|max:20',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:5020',
            'book_file' => 'nullable|file|mimes:pdf,epub,docx|max:20480', // 20MB
            'reading_status' => 'nullable|in:unread,reading,completed',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $cover = $request->file('cover_image');
            $coverName = time() . '_' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $cover->storeAs('public/covers', $coverName);
            $validated['cover_image'] = $coverName;
        }
        // Handle Book File Upload
        if ($request->hasFile('book_file')) {
            $file = $request->file('book_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/books', $fileName);
            
            $validated['book_file'] = $fileName;
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
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
            'book' => $book->load('user', 'category', 'reviews.user')
        ]);
    }

    public function read(Book $book)
    {
        // Check if user owns this book or it's public
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Books/Read', [
            'book' => $book
        ]);
    }

    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book->load('user', 'category')
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
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:5120',
            'book_file' => 'nullable|file|mimes:pdf,epub,docx|max:20480',
            'reading_status' => 'nullable|in:unread,reading,completed',
            'reading_progress' => 'nullable|integer|min:0|max:100',
        ]);


        // Handle Cover Image Upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover
            if ($book->cover_image) {
                Storage::delete('public/covers/' . $book->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $coverName = time() . '_' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $cover->storeAs('public/covers', $coverName);
            $validated['cover_image'] = $coverName;
        }

        // Handle Book File Upload
        if ($request->hasFile('book_file')) {
            // Delete old file
            if ($book->book_file) {
                Storage::delete('public/books/' . $book->book_file);
            }
            
            $file = $request->file('book_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/books', $fileName);
            
            $validated['book_file'] = $fileName;
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Delete a book
     */
    public function destroy(Book $book)
    {
        // Delete associated files
        if ($book->cover_image) {
            Storage::delete('public/covers/' . $book->cover_image);
        }
        if ($book->book_file) {
            Storage::delete('public/books/' . $book->book_file);
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }

    public function download(Book $book)
    {
        if ($book->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$book->book_file) {
            abort(404, 'Book file not found');
        }

        $filePath = storage_path('app/public/books/' . $book->book_file);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $book->title . '.' . pathinfo($book->book_file, PATHINFO_EXTENSION));
    }

    public function updateProgress(Request $request, Book $book)
    {
        $request->validate([
            'reading_progress' => 'required|integer|min:0|max:100',
            'reading_status' => 'required|in:unread,reading,completed',
        ]);

        $book->update([
            'reading_progress' => $request->reading_progress,
            'reading_status' => $request->reading_status,
        ]);

        return response()->json(['message' => 'Progress updated!']);
    }

    public function offline()
    {
        $books = Book::where('user_id', Auth::id())
            ->whereNotNull('book_file')
            ->get();

        return Inertia::render('Books/Offline', [
            'books' => $books
        ]);
    }



    public function __construct() 
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
}
