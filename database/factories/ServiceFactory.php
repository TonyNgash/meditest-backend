<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'test_code'=>'MDS0010',
            'test_name'=>'ALBUMIN,SERUM',
            'test_price'=>800.00,
            'test_constituents'=>'ALBUMIN, SERUM',
            'test_category_id'=>'3',
            'test_category'=>'KIDNEY RELATED DISORDERS',
            'test_prerequisites'=>'12 hours fasting is recommended',
            'test_report_availability'=>'Same Day',
            'test_desc'=>'Albumin is the main protein of human blood plasma. it serves to transport vitamins, minerals and hormones. albumin level increases in dehydration, poor diet, diarrhea, fever, hypoglycemia and burn cases. serum albumin measurements are used in the monitoring and treatment of numerous diseases involving those related to nutrition and pathology particularly in the liver and kidney.',
            'home' => true,
            'creator_job_id'=>'5',
            'status'=>'active',
            'image_path'=>$this->faker->imageUrl($width = 640, $height = 480,'cat',true)
        ];
    }
}
