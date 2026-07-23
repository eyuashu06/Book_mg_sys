<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    // Define which fields can be mass-assigned
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'google_books_id',
        'published_year',
        'description',
        'cover_image',
        'book_file',
        'file_type',
        'file_size',
        'preview_link',
        'reading_status',
        'reading_progress',
        'metadata',
        'google_books_data',
        'user_id',
        'category_id',
    ];

    // Optional: Define any relationships here
    // Example: public function author() { return $this->belongsTo(Author::class); }

    protected $casts = [
        'metadata' => 'array',
        'google_books_data' => 'array',
        'file_size' => 'integer',
        'reading_progress' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('reading_status', 'unread');
    }

    public function scopeReading($query)
    {
        return $query->where('reading_status', 'reading');
    }

    public function scopeCompleted($query)
    {
        return $query->where('reading_status', 'completed');
    }

    // Accessors
    public function getCoverUrlAttribute()
    {
        return $this->cover_image 
            ? asset('storage/covers/' . $this->cover_image) 
            : asset('images/default-cover.jpg');
    }

    public function getFileUrlAttribute()
    {
        return $this->book_file 
            ? asset('storage/books/' . $this->book_file) 
            : null;
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $this->file_size > 0 ? floor(log($this->file_size, 1024)) : 0;
        
        return number_format($this->file_size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
    // Optional: Define accessors or mutators
    public function getTitleUppercaseAttribute()
    {
        return strtoupper($this->title);
    }
}
