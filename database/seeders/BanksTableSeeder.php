<?php

namespace Database\Seeders;

use App\Imports\BanksImport;
use App\Models\Bank;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new BanksImport, 'banklist.xlsx');
    }
}
