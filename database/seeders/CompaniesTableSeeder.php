<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $company = Company::create([
            'name' => "CY Industries"
        ]);

        $bank = Bank::where('swift', 'OCBCSGSGXXX')->first();
        $accountNumber = '687586784001';
        $company->banks()->attach([
            $bank->id => [
                'account_number' => $accountNumber,
                'xero_account_code' => '090',
                'default' => true
            ]
        ]);
    }
}
