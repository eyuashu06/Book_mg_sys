<?php

namespace Tests\Feature;

use Tests\TestCase;

class BookRoutesTest extends TestCase
{
    public function test_create_book_route_is_not_shadowed_by_the_book_show_route(): void
    {
        $response = $this->get('/books/create');

        $response->assertRedirect('/login');
    }
}
