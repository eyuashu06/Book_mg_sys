<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);

        $review = Review::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => Auth::id()],
            $validated
        );

        return redirect()->back()->with('success', 'Review submitted!');
    }

    public function update(Request $request, Book $book, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);

        $review->update($validated);

        return redirect()->back()->with('success', 'Review updated!');
    }

    public function destroy(Book $book, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted!');
    }
}
