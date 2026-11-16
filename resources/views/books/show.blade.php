@extends('layouts.app')

@section('content')
    <article class="book-detail">
        <span class="tag">{{ $book->genre ?? 'General' }}</span>
        @if ($book->cover)
            <img
                src="{{ str_starts_with($book->cover, 'http') ? $book->cover : Storage::url($book->cover) }}"
                alt="Cover for {{ $book->title }}"
            >
        @else
            <div class="card-image-placeholder">No Cover Available</div>
        @endif
        <h1>{{ $book->title ?? 'Untitled' }}</h1>
        <p>by {{ $book->author ?? 'Unknown author' }}</p>
        <p>Published by: {{ $book->publisher->name ?? 'Unknown' }}</p>
    </article>
@endsection
