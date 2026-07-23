@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">📊 Book Management Dashboard</h2>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold">Total Books</h3>
                        <p class="text-4xl font-bold text-blue-600">{{ $totalBooks ?? 0 }}</p>
                    </div>
                    <div class="bg-green-100 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold">Authors</h3>
                        <p class="text-4xl font-bold text-green-600">{{ $booksByYear->count() ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold">Years Covered</h3>
                        <p class="text-4xl font-bold text-purple-600">
                            @if(isset($booksByYear) && $booksByYear->count() > 0)
                                {{ $booksByYear->first()->published_year ?? 'N/A' }} - {{ $booksByYear->last()->published_year ?? 'N/A' }}
                            @else
                                0
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Recent Books -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">📚 Recent Books</h3>
                    @if(isset($recentBooks) && $recentBooks->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($recentBooks as $book)
                                <div class="flex justify-between items-center border-b pb-2">
                                    <div>
                                        <span class="font-medium">{{ $book->title }}</span>
                                        <span class="text-gray-600 text-sm">by {{ $book->author }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $book->published_year }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No books added yet.</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-4">
                    <a href="{{ route('books.create') }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        ➕ Add New Book
                    </a>
                    <a href="{{ route('books.index') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        📖 View All Books
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection