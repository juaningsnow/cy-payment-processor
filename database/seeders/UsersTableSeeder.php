<?php

namespace Database\Seeders;

use App\Models\Company;
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
        $user = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@cy-bm.sg',
            'is_admin' => true,
            'password' => bcrypt('admin'),
        ]);
        
        Company::all()->each(function ($company, $key) use ($user) {
            if ($key > 0) {
                // $user->companies()->attach($company, [
                //     'is_active' => false
                // ]);
            } else {
                $user->companies()->attach($company, [
                    'is_active' => true
                ]);
            }
        });
    }
}
