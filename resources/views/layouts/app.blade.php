<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Student Registration') &mdash; {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
        <nav class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4 sm:px-6">
                <a href="{{ route('students.index') }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                    Student Registration
                </a>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                        Students
                    </a>
                    <a href="{{ route('students.create') }}" class="rounded-md bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                        Register
                    </a>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-300">
                    <span aria-hidden="true">&#9989;</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
