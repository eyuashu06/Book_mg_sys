@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
        <div class="mb-6">
            <h2 class="text-3xl font-bold mb-2">{{ $book->title }}</h2>
            <p class="text-xl text-gray-600">✍️ {{ $book->author }}</p>
        </div>

        <div class="space-y-3 border-t border-b py-4 my-4">
            <p><span class="font-semibold">ISBN:</span> {{ $book->isbn }}</p>
            <p><span class="font-semibold">Published Year:</span> {{ $book->published_year }}</p>
            @if($book->description)
                <p><span class="font-semibold">Description:</span></p>
                <p class="text-gray-700">{{ $book->description }}</p>
            @endif
        </div>

        <div class="flex space-x-4 mt-4">
            <a href="{{ route('books.edit', $book) }}" 
               class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Edit
            </a>
            <form action="{{ route('books.destroy', $book) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this book?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Delete
                </button>
            </form>
            <a href="{{ route('books.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to All Books
            </a>
        </div>
    </div>
@endsection
