<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookStoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication needed)
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index'])->name('books.list');

// Authenticated routes (must include /books/create BEFORE /books/{book})
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Book Management (CRUD) - Only authenticated users
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Reading & Progress
    Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');
    Route::post('/books/{book}/progress', [BookController::class, 'updateProgress'])->name('books.progress');
    Route::get('/books/{book}/download', [BookController::class, 'download'])->name('books.download');

    // Offline Books
    Route::get('/offline', [BookController::class, 'offline'])->name('books.offline');

    // Reviews
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/books/{book}/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/books/{book}/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Book Store (Google Books API)
    Route::get('/bookstore', [BookStoreController::class, 'search'])->name('bookstore.search');
    Route::get('/bookstore/{slug}', [BookStoreController::class, 'show'])->name('bookstore.show');
    Route::post('/bookstore/import', [BookStoreController::class, 'import'])->name('bookstore.import');
});

// Public book detail route (must come after any /books/{book}/... routes)
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
