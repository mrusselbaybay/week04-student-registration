<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * A name may only contain letters, spaces, hyphens, and apostrophes.
     */
    private const NAME_PATTERN = "/^[\p{L}\s'-]+$/u";

    /**
     * The Bachelor of Science programs offered by the college.
     *
     * @var array<int, string>
     */
    public const PROGRAMS = [
        'BS Biology',
        'BS Chemistry',
        'BS Mathematics',
        'BS Psychology',
        'BS Office Administration',
        'BS Entrepreneurship',
        'BS Accountancy',
        'BS Information Technology',
        'BS Computer Science',
        'BS Criminology',
        'BS Electronics Engineering',
        'BS Mechanical Engineering',
        'BS Electrical Engineering',
        'BS Civil Engineering',
        'BS Computer Engineering',
        'BS Industrial Technology',
        'BS Hospitality Management',
        'BS Tourism Management',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_number' => ['required', 'regex:/^\d{4}-\d{4}$/', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
            'last_name' => ['required', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
            'email' => ['required', 'email', 'unique:students,email'],
            'mobile_number' => ['required', 'digits_between:10,11'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'program' => ['required', 'in:'.implode(',', self::PROGRAMS)],
            'year_level' => ['required', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:100'],
            'municipality_city' => ['required', 'string', 'max:100'],
            'barangay' => ['required', 'string', 'max:100'],
            'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_number' => 'student number',
            'date_of_birth' => 'date of birth',
            'mobile_number' => 'mobile number',
            'year_level' => 'year level',
            'municipality_city' => 'municipality/city',
            'profile_picture' => 'profile picture',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_number.regex' => 'The student number must follow the format 0000-0000.',
            'first_name.regex' => 'The first name may only contain letters.',
            'middle_name.regex' => 'The middle name may only contain letters.',
            'last_name.regex' => 'The last name may only contain letters.',
            'mobile_number.digits_between' => 'The mobile number must be 10 to 11 digits and contain numbers only.',
            'date_of_birth.before' => 'The date of birth cannot be in the future.',
            'program.in' => 'Please select a valid program from the list.',
            'profile_picture.required' => 'Please attach a profile picture.',
            'profile_picture.image' => 'The profile picture must be a JPG or PNG image.',
        ];
    }
}
