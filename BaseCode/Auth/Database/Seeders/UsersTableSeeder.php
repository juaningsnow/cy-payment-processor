<?php

namespace BaseCode\Auth\Database\Seeders;

use BaseCode\Auth\Services\UserRecordService;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        UserRecordService::create(
            'admin',
            'admin@email.com',
            'admin',
            ['Admin']
            // [app()->make(Roles::class)->getById(1)]
        );
    }
}
