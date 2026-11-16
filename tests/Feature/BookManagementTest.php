<?php

use App\Models\Book;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests can browse books but must sign in to manage them', function () {
    $this->get(route('books.index'))
        ->assertOk();

    $this->get(route('books.create'))
        ->assertRedirect(route('login'));
});

test('a manager can sign in with a valid password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('A-valid-password-123'),
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'A-valid-password-123',
    ])
        ->assertRedirect(route('books.index'));

    $this->assertAuthenticatedAs($user);
});

test('an authenticated user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('books.index'));

    $this->assertGuest();
});

test('guests can view the login form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Manager Login');
});

test('authenticated users can open the book forms and detail page', function () {
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Learning Web Layout',
        'author' => 'Alex Rivera',
        'genre' => 'Education',
    ]);

    $this->actingAs($user)
        ->get(route('books.create'))
        ->assertOk()
        ->assertSee('Add New Book');

    $this->get(route('books.edit', $book))
        ->assertOk()
        ->assertSee('Edit Book');

    $this->get(route('books.show', $book))
        ->assertOk()
        ->assertSee('Learning Web Layout');
});

test('an authenticated user can add a book with an uploaded cover', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $publisher = Publisher::create(['name' => 'Campus Press']);
    $cover = UploadedFile::fake()->create('cover.jpg', 10, 'image/jpeg');

    $response = $this->actingAs($user)->post(route('books.store'), [
        'title' => 'Learning Web Layout',
        'author' => 'Alex Rivera',
        'genre' => 'Education',
        'publisher_id' => $publisher->id,
        'cover' => $cover,
    ]);

    $response->assertRedirect(route('books.index'));

    $book = Book::query()->firstOrFail();
    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Learning Web Layout',
        'publisher_id' => $publisher->id,
    ]);
    expect($book->cover)->toStartWith('covers/');
    Storage::disk('public')->assertExists($book->cover);
});

test('an authenticated user can add a book with an external cover url', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), [
            'title' => 'Learning Web Layout',
            'author' => 'Alex Rivera',
            'genre' => 'Education',
            'cover' => 'https://example.test/cover.jpg',
        ])
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseHas('books', [
        'title' => 'Learning Web Layout',
        'cover' => 'https://example.test/cover.jpg',
    ]);
});

test('an invalid book cover is rejected', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), [
            'title' => 'Learning Web Layout',
            'author' => 'Alex Rivera',
            'genre' => 'Education',
            'cover' => UploadedFile::fake()->create('cover.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHasErrors(['cover']);

    $this->assertDatabaseCount('books', 0);
    Storage::disk('public')->assertEmpty();
});

test('updating a book replaces its old cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/old.jpg', 'old cover');
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Old title',
        'author' => 'Alex Rivera',
        'genre' => 'Education',
        'cover' => 'covers/old.jpg',
    ]);

    $response = $this->actingAs($user)->put(route('books.update', $book), [
        'title' => 'Updated title',
        'author' => 'Alex Rivera',
        'genre' => 'Education',
        'cover' => UploadedFile::fake()->create('new-cover.png', 10, 'image/png'),
    ]);

    $response->assertRedirect(route('books.index'));

    $book->refresh();
    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'title' => 'Updated title',
    ]);
    Storage::disk('public')->assertExists($book->cover);
    Storage::disk('public')->assertMissing('covers/old.jpg');
});

test('deleting a book also deletes its uploaded cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/delete-me.jpg', 'cover');
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Book to delete',
        'author' => 'Alex Rivera',
        'genre' => 'Education',
        'cover' => 'covers/delete-me.jpg',
    ]);

    $this->actingAs($user)
        ->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
    Storage::disk('public')->assertMissing('covers/delete-me.jpg');
});

test('the book list is paginated', function () {
    for ($index = 1; $index <= 6; $index++) {
        Book::create([
            'title' => "Book {$index}",
            'author' => 'Alex Rivera',
            'genre' => 'Education',
        ]);
    }

    $this->get(route('books.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Book 6')
        ->assertDontSee('Book 1');
});
