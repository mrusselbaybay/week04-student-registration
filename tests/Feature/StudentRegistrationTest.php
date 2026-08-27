<?php

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function validStudentPayload(array $overrides = []): array
{
    return array_merge([
        'student_id' => '24-0001-IT',
        'first_name' => 'Juan',
        'middle_name' => 'Santos',
        'last_name' => 'Dela Cruz',
        'email' => 'juan.delacruz@example.com',
        'mobile_number' => '09171234567',
        'date_of_birth' => '2002-05-14',
        'gender' => 'Male',
        'program' => 'BS Information Technology',
        'year_level' => '1st Year',
        'address' => '123 Rizal Street, Manila',
        'profile_picture' => UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg'),
    ], $overrides);
}

test('registration form is displayed', function () {
    $this->get(route('students.create'))->assertOk();
});

test('a student can register with valid data', function () {
    Storage::fake('public');

    $response = $this->post(route('students.store'), validStudentPayload());

    $student = Student::sole();

    $response->assertRedirect(route('students.show', $student));
    $response->assertSessionHas('success', 'Student registered successfully!');

    expect($student)
        ->student_id->toBe('24-0001-IT')
        ->email->toBe('juan.delacruz@example.com');

    Storage::disk('public')->assertExists($student->profile_picture);
});

test('registration requires all mandatory fields', function () {
    $response = $this->post(route('students.store'), []);

    $response->assertSessionHasErrors([
        'student_id', 'first_name', 'last_name', 'email', 'mobile_number',
        'date_of_birth', 'gender', 'program', 'year_level', 'address', 'profile_picture',
    ]);

    expect(Student::count())->toBe(0);
});

test('student id must be unique', function () {
    Storage::fake('public');
    Student::factory()->create(['student_id' => '24-0001-IT']);

    $response = $this->post(route('students.store'), validStudentPayload());

    $response->assertSessionHasErrors('student_id');
});

test('email must be unique', function () {
    Storage::fake('public');
    Student::factory()->create(['email' => 'juan.delacruz@example.com']);

    $response = $this->post(route('students.store'), validStudentPayload());

    $response->assertSessionHasErrors('email');
});

test('profile picture must be an image', function () {
    Storage::fake('public');

    $response = $this->post(route('students.store'), validStudentPayload([
        'profile_picture' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
    ]));

    $response->assertSessionHasErrors('profile_picture');
});

test('a registered student profile can be viewed', function () {
    Storage::fake('public');
    $student = Student::factory()->create();

    $this->get(route('students.show', $student))
        ->assertOk()
        ->assertSee($student->first_name)
        ->assertSee($student->student_id);
});
