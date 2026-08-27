<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
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
            'student_id' => ['required', 'string', 'max:50', 'unique:students,student_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:students,email'],
            'mobile_number' => ['required', 'numeric', 'digits_between:7,15'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'program' => ['required', 'string', 'max:150'],
            'year_level' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
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
            'student_id' => 'student ID',
            'date_of_birth' => 'date of birth',
            'mobile_number' => 'mobile number',
            'year_level' => 'year level',
            'profile_picture' => 'profile picture',
        ];
    }
}
