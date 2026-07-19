<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookStoreController extends Controller
{
    private const BASE_URL = 'https://www.junkybooks.com';

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];

        if ($query) {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->timeout(15)->get(self::BASE_URL . '/book/search.php', [
                'search' => $query,
            ]);

            if ($response->successful()) {
                $results = $this->parseSearchResults($response->body());
            }
        }

        return Inertia::render('Books/BookStore', [
            'results' => $results,
            'query' => $query,
        ]);
    }

    public function show($slug)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ])->timeout(15)->get(self::BASE_URL . '/book/' . $slug);

        if (!$response->successful()) {
            return redirect()->route('bookstore.search')
                ->with('error', 'Could not fetch book details.');
        }

        $book = $this->parseBookPage($response->body(), $slug);

        return Inertia::render('Books/BookDetail', [
            'book' => $book,
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'slug' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:500',
            'pdf_url' => 'nullable|string|max:500',
            'reader_url' => 'nullable|string|max:500',
        ]);

        $coverName = null;
        if ($request->cover_image) {
            try {
                $coverResponse = Http::timeout(10)->get($request->cover_image);
                if ($coverResponse->successful()) {
                    $coverName = 'imported_' . time() . '_' . uniqid() . '.jpg';
                    Storage::put('public/covers/' . $coverName, $coverResponse->body());
                }
            } catch (\Exception $e) {
                $coverName = null;
            }
        }

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => null,
            'google_books_id' => $validated['slug'],
            'published_year' => date('Y'),
            'description' => $validated['description'],
            'cover_image' => $coverName,
            'preview_link' => $validated['reader_url'] ?? ($validated['pdf_url'] ?? null),
            'metadata' => [
                'source' => 'junkybooks',
                'slug' => $validated['slug'],
                'pdf_url' => $validated['pdf_url'],
                'reader_url' => $validated['reader_url'],
            ],
            'user_id' => Auth::id(),
            'reading_status' => 'unread',
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book imported from JunkyBooks!');
    }

    private function stripPrefix($path)
    {
        if (str_starts_with($path, '../')) {
            return substr($path, 3);
        }
        if (str_starts_with($path, './')) {
            return substr($path, 2);
        }
        return $path;
    }

    private function loadHTMLSafe($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $html = preg_replace('/[^\x{0000}-\x{FFFF}]/u', '', $html);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        return $dom;
    }

    private function parseSearchResults($html): array
    {
        $results = [];
        $dom = $this->loadHTMLSafe($html);
        $xpath = new \DOMXPath($dom);
        $products = $xpath->query("//div[contains(@class, 'product-wrapper')]");

        foreach ($products as $product) {
            $linkNode = $xpath->query(".//div[contains(@class, 'product-img')]/a", $product)->item(0);
            $imgNode = $xpath->query(".//div[contains(@class, 'product-img')]//img", $product)->item(0);
            $titleNode = $xpath->query(".//div[contains(@class, 'product-details')]//a", $product)->item(0);

            if (!$linkNode || !$titleNode) continue;

            $href = $linkNode->getAttribute('href');
            $slug = ltrim($href, '/');
            if (str_starts_with($slug, 'book/')) {
                $slug = substr($slug, 5);
            }

            $title = trim($titleNode->textContent);
            $coverUrl = null;
            if ($imgNode) {
                $coverUrl = $imgNode->getAttribute('src');
                if ($coverUrl && !str_starts_with($coverUrl, 'http')) {
                    $coverUrl = self::BASE_URL . '/' . $this->stripPrefix($coverUrl);
                }
            }

            $author = 'JunkyBooks';
            $pieces = explode(' By ', $title, 2);
            if (isset($pieces[1])) {
                $title = trim($pieces[0]);
                $author = trim($pieces[1]);
            }

            $results[] = [
                'slug' => $slug,
                'title' => $title,
                'author' => $author,
                'cover_image' => $coverUrl,
                'preview_link' => self::BASE_URL . '/book/' . $slug,
                'source' => 'junkybooks',
            ];

            if (count($results) >= 20) break;
        }

        return $results;
    }

    private function parseBookPage($html, $slug): array
    {
        $book = [
            'slug' => $slug,
            'title' => 'Unknown Title',
            'author' => 'JunkyBooks',
            'description' => '',
            'cover_image' => null,
            'preview_link' => self::BASE_URL . '/book/' . $slug,
            'pdf_url' => null,
            'reader_url' => null,
            'category' => null,
            'source' => 'junkybooks',
        ];

        $dom = $this->loadHTMLSafe($html);
        $xpath = new \DOMXPath($dom);

        $titleNode = $xpath->query("//h1")->item(0);
        if ($titleNode) {
            $book['title'] = trim($titleNode->textContent);
        }

        $authorNode = $xpath->query("//div[contains(@class, 'product-info-stock-sku')]//a");
        if ($authorNode->length > 0) {
            $authorText = trim($authorNode->item(0)->textContent);
            $book['author'] = str_replace('By: ', '', $authorText);
        }

        $categoryNode = $xpath->query("//div[contains(@class, 'reviews-actions')]//a");
        if ($categoryNode->length > 0) {
            $book['category'] = trim(str_replace('Category: ', '', $categoryNode->item(0)->textContent));
        }

        $descNode = $xpath->query("//div[contains(@class, 'product-addto-links-text')]//p");
        if ($descNode->length > 0) {
            $book['description'] = trim($descNode->item(0)->textContent);
        }

        $imgNode = $xpath->query("//div[contains(@class, 'flexslider')]//li[1]/img");
        if ($imgNode->length > 0) {
            $src = $imgNode->item(0)->getAttribute('src');
            if ($src) {
                $book['cover_image'] = self::BASE_URL . '/' . $this->stripPrefix($src);
            }
        }

        foreach ($xpath->query("//a") as $link) {
            $href = $link->getAttribute('href');
            if (str_contains($href, 'reader.php?book=')) {
                $book['reader_url'] = self::BASE_URL . '/book/' . $href;
                break;
            }
        }

        foreach ($xpath->query("//a[@download]") as $link) {
            $href = $link->getAttribute('href');
            if ($href && !str_contains($href, 'reader.php')) {
                if (!str_starts_with($href, 'http')) {
                    $book['pdf_url'] = self::BASE_URL . '/' . $this->stripPrefix($href);
                } else {
                    $book['pdf_url'] = $href;
                }
                break;
            }
        }

        $pieces = explode(' By ', $book['title'], 2);
        if (isset($pieces[1])) {
            $book['title'] = trim($pieces[0]);
            $book['author'] = trim($pieces[1]);
        }

        return $book;
    }
}
