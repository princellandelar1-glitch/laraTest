@extends('layouts.app')

@section('content')
    <h1>Add New Book</h1>

    <form class="form-panel" method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="title">Book title</label>
        <input id="title" type="text" name="title" maxlength="150" required value="{{ old('title') }}">
        @error('title')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="author">Book author</label>
        <input id="author" type="text" name="author" maxlength="100" required value="{{ old('author') }}">
        @error('author')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="genre">Book genre</label>
        <input id="genre" type="text" name="genre" maxlength="50" required value="{{ old('genre') }}">
        @error('genre')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="cover">Book cover</label>
        <input id="cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp">
        @error('cover')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="publisher_id">Publisher</label>
        <select name="publisher_id" id="publisher_id">
            <option value="">Select publisher</option>
            @foreach ($publishers as $publisher)
                <option value="{{ $publisher->id }}" @selected(old('publisher_id') == $publisher->id)>
                    {{ $publisher->name }}
                </option>
            @endforeach
        </select>
        @error('publisher_id')
            <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit">Add Book</button>
    </form>
@endsection
