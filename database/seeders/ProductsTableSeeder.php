<?php

namespace Database\Seeders;

use App\Models\ProductModel;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductModel::factory()->count(10)->create();
    }
}
