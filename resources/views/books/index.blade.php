@extends('layouts.app')

@section('title', 'All Books')

@section('content')
    @auth
        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6">
            <p class="text-blue-800">👋 Welcome back, {{ Auth::user()->name }}!</p>
            <p class="text-sm text-blue-600">Manage your books from the <a href="{{ route('dashboard') }}" class="underline">dashboard</a>.</p>
        </div>
    @endauth

    <h2 class="text-3xl font-bold mb-6">All Books</h2>

    <!-- Search Form -->
    <form action="{{ route('books.index') }}" method="GET" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="search" placeholder="Search books..." 
                   value="{{ request('search') }}"
                   class="flex-1 border border-gray-300 rounded px-4 py-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Search
            </button>
        </div>
    </form>

    @if($books->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500">No books found. Add your first book!</p>
            @auth
                <a href="{{ route('books.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add New Book
                </a>
            @endauth
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">{{ $book->title }}</h3>
                    <p class="text-gray-600 mb-1">✍️ {{ $book->author }}</p>
                    <p class="text-gray-500 text-sm mb-1">ISBN: {{ $book->isbn }}</p>
                    <p class="text-gray-500 text-sm mb-3">📅 {{ $book->published_year }}</p>
                    
                    @auth
                        <div class="flex space-x-2">
                            <a href="{{ route('books.show', $book) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                View
                            </a>
                            <a href="{{ route('books.edit', $book) }}" 
                               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                Edit
                            </a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this book?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('books.show', $book) }}" 
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                            View
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
        
        @if(method_exists($books, 'links'))
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    @endif
@endsection