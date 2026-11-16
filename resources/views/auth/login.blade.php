@extends('layouts.app')

@section('content')
    <h1>Manager Login</h1>

    <form class="form-panel" method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" maxlength="255" required value="{{ old('email') }}">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" minlength="12" maxlength="255" required>
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit">Login</button>
    </form>
@endsection
