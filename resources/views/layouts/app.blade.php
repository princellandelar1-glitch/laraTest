<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshelf</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="site-header">
        <a href="{{ route('books.index') }}">Bookshelf</a>
        @guest
            <a href="{{ route('login') }}">Login</a>
        @endguest

        @auth
            <span>Hello, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        @endauth
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>