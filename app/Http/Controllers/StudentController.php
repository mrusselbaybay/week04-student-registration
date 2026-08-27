<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of the registered students.
     */
    public function index(): View
    {
        $students = Student::latest()->paginate(10);

        return view('students.index', compact('students'));
    }

    /**
     * Show the student registration form.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Validate and store a newly registered student.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['profile_picture'] = $request->file('profile_picture')
            ->store('profile_pictures', 'public');

        $student = Student::create($validated);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student registered successfully!');
    }

    /**
     * Display the registered student's profile.
     */
    public function show(Student $student): View
    {
        return view('students.show', compact('student'));
    }
}
