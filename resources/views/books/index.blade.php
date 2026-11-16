@extends('layouts.app')

@section('content')
    <h1>Book Collection</h1>
    <p>Browse and manage books in our database</p>

    @auth
        <a class="button" href="{{ route('books.create') }}">Add Book</a>
    @endauth

    @session('success')
        <p class="success">{{ session('success') }}</p>
    @endsession

    <div class="book-grid">
        @forelse ($books as $book)
            <article class="card">
                <span class="tag">{{ $book->genre ?? 'General' }}</span>
                <a href="{{ route('books.show', $book) }}" class="card-image-link">
                    @if ($book->cover)
                        <img
                            src="{{ str_starts_with($book->cover, 'http') ? $book->cover : Storage::url($book->cover) }}"
                            alt="Cover for {{ $book->title }}"
                            class="card-image"
                        >
                    @else
                        <div class="card-image-placeholder">No Cover Available</div>
                    @endif
                </a>
                <h2 class="card-title">
                    <a href="{{ route('books.show', $book) }}">{{ $book->title ?? 'Untitled' }}</a>
                </h2>
                <p class="card-author">by {{ $book->author ?? 'Unknown author' }}</p>
                <p>Published by: {{ $book->publisher->name ?? 'Unknown' }}</p>

                @auth
                    <div class="card-actions">
                        <a class="button button-secondary" href="{{ route('books.edit', $book) }}">Edit Book</a>
                    </div>
                    <form action="{{ route('books.destroy', $book) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="button button-danger" type="submit">Delete</button>
                    </form>
                @endauth
            </article>
        @empty
            <p>No books are available yet.</p>
        @endforelse
    </div>

    @if ($books->hasPages())
        <nav aria-label="Book pages">
            @if ($books->onFirstPage())
                <span aria-disabled="true">Previous</span>
            @else
                <a href="{{ $books->previousPageUrl() }}">Previous</a>
            @endif

            <span>Page {{ $books->currentPage() }} of {{ $books->lastPage() }}</span>

            @if ($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}">Next</a>
            @else
                <span aria-disabled="true">Next</span>
            @endif
        </nav>
    @endif
@endsection
