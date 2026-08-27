@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Student Registration</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Fill out the form below to register as a student of the College of Information Technology.
        </p>

        @if ($errors->any())
            <div class="mt-6 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
                <p class="font-medium">Please fix the following errors:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-8">
            @csrf

            <fieldset class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <legend class="col-span-full text-sm font-semibold text-gray-900 dark:text-white">Student Information</legend>

                <x-form-group name="student_id" label="Student ID">
                    <x-text-input name="student_id" placeholder="e.g. 24-0001-IT" />
                </x-form-group>

                <x-form-group name="email" label="Email Address">
                    <x-text-input name="email" type="email" placeholder="student@example.com" />
                </x-form-group>

                <x-form-group name="first_name" label="First Name">
                    <x-text-input name="first_name" />
                </x-form-group>

                <x-form-group name="middle_name" label="Middle Name (optional)">
                    <x-text-input name="middle_name" />
                </x-form-group>

                <x-form-group name="last_name" label="Last Name">
                    <x-text-input name="last_name" />
                </x-form-group>

                <x-form-group name="mobile_number" label="Mobile Number">
                    <x-text-input name="mobile_number" type="tel" placeholder="09XXXXXXXXX" />
                </x-form-group>

                <x-form-group name="date_of_birth" label="Date of Birth">
                    <x-text-input name="date_of_birth" type="date" />
                </x-form-group>

                <x-form-group name="gender" label="Gender">
                    <select
                        name="gender"
                        id="gender"
                        class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('gender') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                    >
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                        @foreach (['Male', 'Female', 'Other'] as $option)
                            <option value="{{ $option }}" @selected(old('gender') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </x-form-group>
            </fieldset>

            <fieldset class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <legend class="col-span-full text-sm font-semibold text-gray-900 dark:text-white">Academic Information</legend>

                <x-form-group name="program" label="Program">
                    <x-text-input name="program" placeholder="e.g. BS Information Technology" />
                </x-form-group>

                <x-form-group name="year_level" label="Year Level">
                    <select
                        name="year_level"
                        id="year_level"
                        class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('year_level') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                    >
                        <option value="" disabled {{ old('year_level') ? '' : 'selected' }}>Select year level</option>
                        @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $option)
                            <option value="{{ $option }}" @selected(old('year_level') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </x-form-group>

                <x-form-group name="address" label="Address">
                    <textarea
                        name="address"
                        id="address"
                        rows="3"
                        class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('address') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                    >{{ old('address') }}</textarea>
                </x-form-group>
            </fieldset>

            <fieldset>
                <legend class="text-sm font-semibold text-gray-900 dark:text-white">Profile Picture</legend>

                <div class="mt-4">
                    <x-form-group name="profile_picture" label="Upload a profile picture (JPG or PNG, max 2MB)">
                        <input
                            type="file"
                            name="profile_picture"
                            id="profile_picture"
                            accept="image/png, image/jpeg"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-gray-200"
                        >
                    </x-form-group>
                </div>
            </fieldset>

            <div class="flex justify-end border-t border-gray-200 pt-6 dark:border-gray-700">
                <button
                    type="submit"
                    class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Register Student
                </button>
            </div>
        </form>
    </div>
@endsection
