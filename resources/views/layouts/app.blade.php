<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <nav class="bg-white px-4 py-2 shadow">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}">
                <img src="{{ asset('public/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-24 h-auto">
            </a>

            <ul class="flex">
                <li class="mr-4">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-gray-900">Home</a>
                </li>
                <li class="mr-4">
                    <a href="#" class="text-gray-700 hover:text-gray-900">About</a>
                </li>
                <li class="mr-4">
                    <a href="#" class="text-gray-700 hover:text-gray-900">Contact</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container mx-auto py-4">
        @yield('content')
    </main>

    <!-- Tailwind JS -->
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>
</body>

</html>
