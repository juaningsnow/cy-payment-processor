<?php

namespace Database\Factories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'category' => $this->faker->randomElement(['Category A', 'Category B', 'Category C']),
            'description' => $this->faker->paragraph($nbSentences = 10, $variableNbSentences = true),
            'date_and_time' => $this->faker->dateTimeBetween($startDate = '-5 years', $endDate = '+5 years', $timezone = null)
        ];
    }
}
