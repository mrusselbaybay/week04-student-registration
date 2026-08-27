@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Student Registration</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                A few quick steps to complete your registration.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
                <p class="font-medium">Please review the highlighted fields before continuing.</p>
            </div>
        @endif

        <ol class="mb-10 flex items-start justify-between">
            @foreach (['Personal', 'Photo', 'Contact & Address', 'Academic', 'Review'] as $index => $label)
                <li data-step-nav class="flex flex-1 cursor-pointer flex-col items-center gap-2 opacity-50 transition-opacity">
                    <span
                        data-step-circle
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 text-sm font-semibold text-gray-500 transition-colors dark:bg-gray-700 dark:text-gray-400"
                    >{{ $index + 1 }}</span>
                    <span class="hidden text-center text-xs font-medium text-gray-600 dark:text-gray-300 sm:block">{{ $label }}</span>
                </li>
            @endforeach
        </ol>

        <form id="registration-form" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg shadow-gray-200/50 dark:border-gray-700 dark:bg-gray-800 dark:shadow-none sm:p-8">

                {{-- Step 1: Personal Information --}}
                <div data-step class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tell us who you are.</p>
                    </div>

                    <x-form-group name="student_number" label="Student Number">
                        <x-text-input
                            name="student_number"
                            inputmode="numeric"
                            placeholder="0000-0000"
                            maxlength="9"
                            pattern="\d{4}-\d{4}"
                            title="Format: 0000-0000"
                            required
                        />
                    </x-form-group>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <x-form-group name="first_name" label="First Name">
                            <x-text-input
                                name="first_name"
                                pattern="[A-Za-zÀ-ÖØ-öø-ÿ'\-\s]+"
                                title="Letters only"
                                required
                            />
                        </x-form-group>

                        <x-form-group name="middle_name" label="Middle Name (optional)">
                            <x-text-input
                                name="middle_name"
                                pattern="[A-Za-zÀ-ÖØ-öø-ÿ'\-\s]+"
                                title="Letters only"
                            />
                        </x-form-group>

                        <x-form-group name="last_name" label="Last Name">
                            <x-text-input
                                name="last_name"
                                pattern="[A-Za-zÀ-ÖØ-öø-ÿ'\-\s]+"
                                title="Letters only"
                                required
                            />
                        </x-form-group>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form-group name="date_of_birth" label="Date of Birth">
                            <x-text-input
                                name="date_of_birth"
                                type="date"
                                max="{{ now()->format('Y-m-d') }}"
                                title="Date of birth cannot be in the future"
                                required
                            />
                        </x-form-group>

                        <x-form-group name="gender" label="Gender">
                            <select
                                name="gender"
                                id="gender"
                                required
                                @error('gender') data-server-error="true" @enderror
                                class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('gender') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                @foreach (['Male', 'Female', 'Other'] as $option)
                                    <option value="{{ $option }}" @selected(old('gender') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </x-form-group>
                    </div>
                </div>

                {{-- Step 2: Profile Picture --}}
                <div data-step class="hidden space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Picture</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Upload a clear photo of yourself.</p>
                    </div>

                    <x-form-group name="profile_picture" label="Profile Picture">
                        <div
                            data-dropzone
                            tabindex="0"
                            role="button"
                            aria-label="Upload profile picture"
                            class="group relative flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-10 text-center transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 {{ $errors->has('profile_picture') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-600 dark:hover:border-blue-500 dark:hover:bg-blue-900/10' }}"
                        >
                            <div data-dropzone-empty class="flex flex-col items-center gap-2">
                                <svg class="h-9 w-9 text-gray-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0-3 3m3-3 3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">JPG or PNG, up to 2MB</p>
                            </div>

                            <div data-dropzone-preview class="hidden flex-col items-center gap-2">
                                <img data-preview-image class="h-24 w-24 rounded-full border border-gray-200 object-cover dark:border-gray-600" alt="Selected profile picture preview">
                                <p data-preview-filename class="max-w-[16rem] truncate text-sm font-medium text-gray-700 dark:text-gray-200"></p>
                                <button type="button" data-action="remove-photo" class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400">
                                    Remove photo
                                </button>
                            </div>

                            <input
                                type="file"
                                name="profile_picture"
                                id="profile_picture"
                                accept="image/png, image/jpeg"
                                class="sr-only"
                                @error('profile_picture') data-server-error="true" @enderror
                                required
                            >
                        </div>
                    </x-form-group>
                </div>

                {{-- Step 3: Contact & Address --}}
                <div data-step class="hidden space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Contact &amp; Address</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">How can we reach you, and where do you live?</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form-group name="email" label="Email Address">
                            <x-text-input name="email" type="email" placeholder="student@example.com" required />
                        </x-form-group>

                        <x-form-group name="mobile_number" label="Mobile Number">
                            <x-text-input
                                name="mobile_number"
                                type="tel"
                                inputmode="numeric"
                                placeholder="09171234567"
                                pattern="\d{10,11}"
                                maxlength="11"
                                title="10 to 11 digits, numbers only"
                                required
                            />
                        </x-form-group>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <x-form-group name="province" label="Province">
                            <x-text-input name="province" required />
                        </x-form-group>

                        <x-form-group name="municipality_city" label="Municipality / City">
                            <x-text-input name="municipality_city" required />
                        </x-form-group>

                        <x-form-group name="barangay" label="Barangay">
                            <x-text-input name="barangay" required />
                        </x-form-group>
                    </div>
                </div>

                {{-- Step 4: Academic Information --}}
                <div data-step class="hidden space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Information</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">What are you enrolling in?</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form-group name="program" label="Program">
                            <select
                                name="program"
                                id="program"
                                required
                                @error('program') data-server-error="true" @enderror
                                class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('program') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                <option value="" disabled {{ old('program') ? '' : 'selected' }}>Select program</option>
                                @foreach (\App\Http\Requests\StoreStudentRequest::PROGRAMS as $option)
                                    <option value="{{ $option }}" @selected(old('program') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </x-form-group>

                        <x-form-group name="year_level" label="Year Level">
                            <select
                                name="year_level"
                                id="year_level"
                                required
                                @error('year_level') data-server-error="true" @enderror
                                class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white {{ $errors->has('year_level') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                            >
                                <option value="" disabled {{ old('year_level') ? '' : 'selected' }}>Select year level</option>
                                @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $option)
                                    <option value="{{ $option }}" @selected(old('year_level') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </x-form-group>
                    </div>
                </div>

                {{-- Step 5: Review --}}
                <div data-step class="hidden space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Review Your Information</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Make sure everything is correct before submitting.</p>
                    </div>

                    <div class="flex justify-center">
                        <img data-review-photo class="hidden h-20 w-20 rounded-full border border-gray-200 object-cover dark:border-gray-600" alt="Profile picture preview">
                    </div>

                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40 sm:grid-cols-2">
                        @foreach ([
                            'student_number' => 'Student Number',
                            'first_name' => 'First Name',
                            'middle_name' => 'Middle Name',
                            'last_name' => 'Last Name',
                            'date_of_birth' => 'Date of Birth',
                            'gender' => 'Gender',
                            'email' => 'Email Address',
                            'mobile_number' => 'Mobile Number',
                            'province' => 'Province',
                            'municipality_city' => 'Municipality / City',
                            'barangay' => 'Barangay',
                            'program' => 'Program',
                            'year_level' => 'Year Level',
                        ] as $field => $label)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                                <dd data-review="{{ $field }}" class="mt-1 text-sm text-gray-900 dark:text-gray-100">—</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="mt-10 flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
                    <button
                        type="button"
                        data-action="back"
                        class="hidden rounded-md border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Back
                    </button>

                    <div class="ml-auto flex gap-3">
                        <button
                            type="button"
                            data-action="next"
                            class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Continue
                        </button>

                        <button
                            type="submit"
                            data-action="submit"
                            class="hidden rounded-md bg-emerald-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            Submit Registration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
