@extends('layouts.app')

@section('title', $student->first_name . ' ' . $student->last_name)

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8">
        <img
            src="{{ Storage::url($student->profile_picture) }}"
            alt="Profile picture of {{ $student->first_name }} {{ $student->last_name }}"
            class="h-24 w-24 shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700"
        >

        <div class="mt-4">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $student->program }} &middot; {{ $student->year_level }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Student Number: {{ $student->student_number }}</p>
        </div>

        <dl class="mt-8 grid grid-cols-1 gap-x-6 gap-y-4 border-t border-gray-200 pt-6 dark:border-gray-700 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Email Address</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mobile Number</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->mobile_number }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Date of Birth</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->date_of_birth->format('F j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Gender</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->gender }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Address</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ $student->barangay }}, {{ $student->municipality_city }}, {{ $student->province }}
                </dd>
            </div>
        </dl>

        <div class="mt-8 flex justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
            <a href="{{ route('students.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                &larr; Back to all students
            </a>
            <a href="{{ route('students.create') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                Register another student
            </a>
        </div>
    </div>
@endsection
