<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $produtc = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->region(),
        ];
    }
}


