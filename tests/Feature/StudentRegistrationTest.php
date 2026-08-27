<?php

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function validStudentPayload(array $overrides = []): array
{
    return array_merge([
        'student_number' => '0124-0439',
        'first_name' => 'Juan',
        'middle_name' => 'Santos',
        'last_name' => 'Dela Cruz',
        'email' => 'juan.delacruz@example.com',
        'mobile_number' => '09171234567',
        'date_of_birth' => '2002-05-14',
        'gender' => 'Male',
        'program' => 'BS Information Technology',
        'year_level' => '1st Year',
        'province' => 'Metro Manila',
        'municipality_city' => 'Quezon City',
        'barangay' => 'Barangay 176',
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
        ->student_number->toBe('0124-0439')
        ->email->toBe('juan.delacruz@example.com')
        ->province->toBe('Metro Manila')
        ->municipality_city->toBe('Quezon City')
        ->barangay->toBe('Barangay 176');

    Storage::disk('public')->assertExists($student->profile_picture);
});

test('registration requires all mandatory fields', function () {
    $response = $this->post(route('students.store'), []);

    $response->assertSessionHasErrors([
        'student_number', 'first_name', 'last_name', 'email', 'mobile_number',
        'date_of_birth', 'gender', 'program', 'year_level', 'province', 'municipality_city',
        'barangay', 'profile_picture',
    ]);

    expect(Student::count())->toBe(0);
});

test('student number must be unique', function () {
    Storage::fake('public');
    Student::factory()->create(['student_number' => '0124-0439']);

    $response = $this->post(route('students.store'), validStudentPayload());

    $response->assertSessionHasErrors('student_number');
});

test('student number must follow the 0000-0000 format', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'student_number' => '12345678',
    ]));

    $response->assertSessionHasErrors('student_number');
});

test('email must be unique', function () {
    Student::factory()->create(['email' => 'juan.delacruz@example.com']);

    $response = $this->post(route('students.store'), validStudentPayload());

    $response->assertSessionHasErrors('email');
});

test('names cannot contain digits', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'first_name' => 'Juan123',
    ]));

    $response->assertSessionHasErrors('first_name');
});

test('mobile number cannot contain letters', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'mobile_number' => '0917abc4567',
    ]));

    $response->assertSessionHasErrors('mobile_number');
});

test('date of birth cannot be in the future', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'date_of_birth' => now()->addDay()->format('Y-m-d'),
    ]));

    $response->assertSessionHasErrors('date_of_birth');
});

test('profile picture must be an image', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'profile_picture' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
    ]));

    $response->assertSessionHasErrors('profile_picture');
});

test('program must be one of the offered programs', function () {
    $response = $this->post(route('students.store'), validStudentPayload([
        'program' => 'BS Underwater Basket Weaving',
    ]));

    $response->assertSessionHasErrors('program');
});

test('a registered student profile can be viewed', function () {
    $student = Student::factory()->create();

    $this->get(route('students.show', $student))
        ->assertOk()
        ->assertSee($student->first_name)
        ->assertSee($student->student_number);
});
