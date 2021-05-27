<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            [
                'name' => 'UOB',
                'code' => '7375',
                'branch_code' => '030'
            ],
            [
                'name' => 'DBS',
                'code' => '7171',
                'branch_code' => '005'
            ],
            [
                'name' => 'OCBC',
                'code' => '7339',
                'branch_code' => '550'
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
