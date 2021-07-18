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
        // Config::create([
        //     'batch_counter' => 1,
        //     'client_id' => 'FE98A33133504C6580ECBC18D2CC9135',
        //     'client_secret' => 'ZeRoIM4DpBUtKBFnu8foxFFCslI0LG0tjQ2rAOILdp1SEQrH',
        //     'access_token' => null,
        //     'refresh_token' => null,
        //     'redirect_url' => 'https://payments.cy-bm.sg/callback',
        //     'scope' => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.attachments'
        // ]);

        Config::create([
            'batch_counter' => 1,
            'client_id' => '4762A1EB358C4896B65255ADADB7A58C',
            'client_secret' => 'MoeB8UFSYqw98AXc69fBmoGn58jR5m-zPCeD9jlRCCBvEKQT',
            'access_token' => null,
            'refresh_token' => null,
            'redirect_url' => 'https://cy.test/callback',
            'scope' => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.attachments'
        ]);
    }
}
