@extends('layouts.app')

@section('contetnt')
     <p>browse and manage books in our database</p>
     <div class="book.grid">
    @foreach ($books as $book)
     <article class="card">
        <span class="tag">{{  $book->genre }}</span>
        <img src="{{ $book->cover }}">
        <h2>{{ $book->title }}</h2>
     </article>    
    @endforeach

    </div>
@endsection