<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'=>"1",
            'self'=>1,
            'dependant_id'=>0,
            'service_id'=>[2,3,4],
            'paid'=>0,
            'scheduled_date'=>'2021-03-02',
            'total_amount'=>'2345.90',
            'seen'=>false
        ];
    }
}
