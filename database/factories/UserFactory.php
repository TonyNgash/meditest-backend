<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstNameMale,
            'sirname' => $this->faker->lastName,
            'last_name' => $this->faker->firstNameMale,
            'gender' => 'male',
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '123456789',
            'phone' => $this->faker->e164PhoneNumber,
            'dob' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'address' => $this->faker->address,
            'remember_token' => Str::random(10),
        ];
    }
}
