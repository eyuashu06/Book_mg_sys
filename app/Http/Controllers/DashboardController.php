<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $recentBooks = Book::latest()->take(5)->get(['id', 'title', 'author', 'published_year']);
        $booksByYear = Book::selectRaw('published_year, count(*) as total')
            ->groupBy('published_year')
            ->orderBy('published_year', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Dashboard', [
            'totalBooks'  => $totalBooks,
            'recentBooks' => $recentBooks,
            'booksByYear' => $booksByYear,
        ]);
    }
}
