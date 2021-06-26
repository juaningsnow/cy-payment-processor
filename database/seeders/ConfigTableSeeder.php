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
            'client_id' => 'FE98A33133504C6580ECBC18D2CC9135',
            'client_secret' => '024VnS98TGQWysteQYrk4UWWZR3QpPBDuYV4-8k8AmTf9tz2',
            'access_token' => null,
            'refresh_token' => null,
            'xero_tenant_id' => null,
            'redirect_url' => 'https://payments.cy-bm.sg/callback',
            'scope' => 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.attachments'
        ]);
    }
}
