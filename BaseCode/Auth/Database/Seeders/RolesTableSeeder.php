<?php

namespace BaseCode\Auth\Database\Seeders;

use BaseCode\Auth\Services\RoleRecordService;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        RoleRecordService::create(
            'Admin',
            []
        );

        RoleRecordService::create(
            'Salesperson',
            [

            ]
        );

        RoleRecordService::create(
            'Purchaser',
            [
            ]
        );
    }
}
