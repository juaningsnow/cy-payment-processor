<?php

namespace Database\Seeders;

use App\Models\Purpose;
use Illuminate\Database\Seeder;

class PurposesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purposes = [
            [
                'name' => 'BEXP',
                'description' => 'Business Expenses',
            ],
            [
                'name' => 'COLL',
                'description' => 'Collection Payment',
            ],
            [
                'name' => 'CSDB',
                'description' => 'Cash Disbursement',
            ],
            [
                'name' => 'GDDS',
                'description' => 'Purchase Sale Of Goods',
            ],
            [
                'name' => 'IHRP',
                'description' => 'Installment Hire Purchase Agreement',
            ],
            [
                'name' => 'INTC',
                'description' => 'Intra Company Payment',
            ],
            [
                'name' => 'IVPT',
                'description' => 'Invoice Payment',
            ],
            [
                'name' => 'SUPP',
                'description' => 'Supplier Payment',
            ],
            [
                'name' => 'TRAD',
                'description' => 'Trade Services',
            ],
            [
                'name' => 'TREA',
                'description' => 'Treasury Payment',
            ],
            [
                'name' => 'FWLV',
                'description' => 'Foreign Worker Levy',
            ],
            [
                'name' => 'LOAN',
                'description' => 'Loan',
            ],
            [
                'name' => 'DIVD',
                'description' => 'Dividend',
            ],
            [
                'name' => 'INTE',
                'description' => 'Interest',
            ],
            [
                'name' => 'OTHR',
                'description' => 'Other',
            ],
            [
                'name' => 'REBT',
                'description' => 'Rebate',
            ],
            [
                'name' => 'REFU',
                'description' => 'Refund',
            ],
            [
                'name' => 'WHLD',
                'description' => 'With Holding',
            ],
            [
                'name' => 'BONU',
                'description' => 'Bonus Payment',
            ],
            [
                'name' => 'COMM',
                'description' => 'Commission',
            ],
            [
                'name' => 'SALA',
                'description' => 'Salary Payment',
            ],
            [
                'name' => 'GSTX',
                'description' => 'Goods & Services Tax',
            ],
            [
                'name' => 'NITX',
                'description' => 'Net Income Tax',
            ],
            [
                'name' => 'PTXP',
                'description' => 'PropertTax',
            ],
            [
                'name' => 'RDTX',
                'description' => 'Road Tax',
            ],
            [
                'name' => 'TAXS',
                'description' => 'Tax Payment',
            ],
        ];

        foreach ($purposes as $purpose) {
            Purpose::create($purpose);
        }
    }
}
