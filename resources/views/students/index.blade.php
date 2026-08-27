@extends('layouts.app')

@section('title', 'Registered Students')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Registered Students</h1>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        @if ($students->isEmpty())
            <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                No students have registered yet.
            </p>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($students as $student)
                    <li>
                        <a href="{{ route('students.show', $student) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <img
                                src="{{ Storage::url($student->profile_picture) }}"
                                alt="Profile picture of {{ $student->first_name }} {{ $student->last_name }}"
                                class="h-10 w-10 shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-600"
                            >
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </p>
                                <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                                    {{ $student->student_number }} &middot; {{ $student->program }}
                                </p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($students->hasPages())
        <div class="mt-6">
            {{ $students->links() }}
        </div>
    @endif
@endsection
