<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                Product::create([
                    'name' => $faker->words(3, true),
                    'description' => $faker->sentence,
                    'price' => $faker->randomFloat(2, 5, 20),
                    'image_url' => $faker->imageUrl($width = 640, $height = 480),
                    'category_id' => $i,
                ]);
            }
        }
    }
}
