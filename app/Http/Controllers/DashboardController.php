<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => Book::where('user_id', $user->id)->count(),
            'reading' => Book::where('user_id', $user->id)
                ->where('reading_status', 'reading')->count(),
            'completed' => Book::where('user_id', $user->id)
                ->where('reading_status', 'completed')->count(),
            'reviews' => Review::whereHas('book', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
        ];

        $recentBooks = Book::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'cover_url' => $book->cover_url,
                    'created_at_from_now' => Carbon::parse($book->created_at)->diffForHumans(),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentBooks' => $recentBooks,
        ]);
    }
}