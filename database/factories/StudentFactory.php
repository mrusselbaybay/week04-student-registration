<?php

namespace Database\Factories;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => $this->faker->unique()->numerify('####-####'),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => $this->faker->numerify('09#########'),
            'date_of_birth' => $this->faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'program' => $this->faker->randomElement(StoreStudentRequest::PROGRAMS),
            'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'province' => $this->faker->randomElement(['Metro Manila', 'Cavite', 'Laguna', 'Bulacan', 'Rizal']),
            'municipality_city' => $this->faker->city(),
            'barangay' => 'Barangay '.$this->faker->numberBetween(1, 176),
            'profile_picture' => 'profile_pictures/placeholder.jpg',
        ];
    }
}
