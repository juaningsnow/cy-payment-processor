<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@cy-bm.sg',
            'bank_id' => Bank::where('swift', 'OCBCSGSGXXX')->first()->id,
            'account_number' => '687586784001',
            'password' => bcrypt('admin'),
        ]);
    }
}
