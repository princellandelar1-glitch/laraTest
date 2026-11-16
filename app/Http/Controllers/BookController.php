<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with('publisher')->paginate(5);

        return view('books.index', compact('books'));
    }

    public function show(Book $book): View
    {
        $book->load('publisher');

        return view('books.show', compact('book'));
    }

    public function create(): View
    {
        $publishers = Publisher::all();

        return view('books.create', compact('publishers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'cover' => $request->hasFile('cover')
                ? ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048']
                : ['nullable', 'url', 'max:2048'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $this->storeCover($request);
        }

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book created successfully!');
    }

    public function edit(Book $book): View
    {
        $publishers = Publisher::all();

        return view('books.edit', compact('book', 'publishers'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'cover' => $request->hasFile('cover')
                ? ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048']
                : ['nullable', 'url', 'max:2048'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
        ]);

        $oldCover = $book->cover;

        if ($request->hasFile('cover')) {
            $validated['cover'] = $this->storeCover($request);
        } elseif (empty($validated['cover'])) {
            unset($validated['cover']);
        }

        $book->update($validated);

        if ($oldCover !== $book->cover) {
            $this->deleteCover($oldCover);
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $cover = $book->cover;
        $book->delete();
        $this->deleteCover($cover);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book deleted successfully');
    }

    private function storeCover(Request $request): string
    {
        $cover = $request->file('cover');

        if ($cover === null) {
            throw new RuntimeException('The uploaded book cover is missing.');
        }

        $path = $cover->store('covers', 'public');

        if ($path === false) {
            throw new RuntimeException('The uploaded book cover could not be stored.');
        }

        return $path;
    }

    private function deleteCover(?string $cover): void
    {
        if ($cover === null || filter_var($cover, FILTER_VALIDATE_URL) !== false) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($cover) && ! $disk->delete($cover)) {
            throw new RuntimeException('The stored book cover could not be deleted.');
        }
    }
}
