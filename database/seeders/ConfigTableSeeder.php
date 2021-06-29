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
        // Config::create([
        //     'batch_counter' => 1,
        //     'client_id' => 'FE98A33133504C6580ECBC18D2CC9135',
        //     'client_secret' => 'ZeRoIM4DpBUtKBFnu8foxFFCslI0LG0tjQ2rAOILdp1SEQrH',
        //     'access_token' => null,
        //     'refresh_token' => null,
        //     'xero_tenant_id' => null,
        //     'redirect_url' => 'https://payments.cy-bm.sg/callback',
        //     'scope' => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.attachments'
        // ]);

        Config::create([
            'batch_counter' => 1,
            'client_id' => '21439E70FFA342D3B0016CE59AF36704',
            'client_secret' => 'c5-0y333j9tgIwTCn-tczXJJ31dUhqIhCJ636FU8q9x0ezfo',
            'access_token' => null,
            'refresh_token' => null,
            'xero_tenant_id' => null,
            'redirect_url' => 'https://cy.test/callback',
            'scope' => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.attachments'
        ]);
    }
}
