<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a book can be created without a cover', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), [
            'title' => 'A Test Book',
            'author' => 'A Test Author',
            'genre' => 'Education',
        ])
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseHas('books', [
        'title' => 'A Test Book',
        'cover' => null,
    ]);
});

test('updating without a new cover keeps the existing cover', function () {
    $book = Book::create([
        'title' => 'A Test Book',
        'author' => 'A Test Author',
        'genre' => 'Education',
        'cover' => 'covers/existing.jpg',
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('books.update', $book), [
            'title' => 'An Updated Book',
            'author' => 'An Updated Author',
            'genre' => 'Technology',
        ])
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'An Updated Book',
        'cover' => 'covers/existing.jpg',
    ]);
});
