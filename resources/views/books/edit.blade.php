@extends('layouts.app')

@section('title', 'Edit Book')

@section('content')
    <h2 class="text-3xl font-bold mb-6">Edit Book</h2>

    <form action="{{ route('books.update', $book) }}" method="POST" class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-medium mb-2">Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="author" class="block text-gray-700 font-medium mb-2">Author *</label>
            <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('author')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="isbn" class="block text-gray-700 font-medium mb-2">ISBN *</label>
            <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('isbn')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="published_year" class="block text-gray-700 font-medium mb-2">Published Year *</label>
            <input type="number" name="published_year" id="published_year" 
                   value="{{ old('published_year', $book->published_year) }}" required
                   min="1900" max="{{ date('Y') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('published_year')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $book->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded hover:bg-yellow-700">
                Update Book
            </button>
            <a href="{{ route('books.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
@endsection
