<?php

namespace Database\Factories;

use App\Models\Phlebo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class PhleboFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Phlebo::class;

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
            'password' => '123456789',
            'phone' => $this->faker->e164PhoneNumber,
            'dob' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
        ];
    }
}
