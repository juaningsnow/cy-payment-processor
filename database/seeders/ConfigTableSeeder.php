<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Config::create([
            'batch_counter' => 1,
            'client_id' => '13B6CD79B9D54DD688B9B0103F644E1A',
            'client_secret' => 'aeBM25VKlvERvz_T1e8CCcVuNFVVErbXXa6uCTbpM5FbB2_v',
            'access_token' => null,
            'refresh_token' => null,
            'xero_tenant_id' => null,
            'redirect_url' => null,
        ]);
    }
}
