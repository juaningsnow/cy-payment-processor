<?php

namespace BaseCode\Auth\Database\Seeders;

use BaseCode\Auth\Services\PermissionRecordService;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class PermissionsTableSeeder extends Seeder
{
    public function run(Faker $faker)
    {
        $modules = [
            [
                'group' => 'mas',
                'name' => 'customer'
            ],
            [
                'group' => 'mas',
                'name' => 'product'
            ],
            [
                'group' => 'mas',
                'name' => 'supplier'
            ],
            [
                'group' => 'mas',
                'name' => 'uom'
            ],
            [
                'group' => 'mas',
                'name' => 'booklet'
            ],
            [
                'group' => 'inv',
                'name' => 'positive adjustment'
            ],
            [
                'group' => 'inv',
                'name' => 'negative adjustment'
            ],
            [
                'group' => 'inv',
                'name' => 'product conversion'
            ],
            [
                'group' => 'pur',
                'name' => 'purchase delivery'
            ],
            [
                'group' => 'sales',
                'name' => 'sale invoice'
            ],
            [
                'group' => 'sales',
                'name' => 'sale returns'
            ],
            [
                'group' => 'rec',
                'name' => 'inbound payments'
            ],
            [
                'group' => 'rpt',
                'name' => 'aging receivables'
            ],
            [
                'group' => 'rpt',
                'name' => 'income statements'
            ],
            [
                'group' => 'rpt',
                'name' => 'statement of account'
            ],
            [
                'group' => 'sttngs',
                'name' => 'user'
            ],
            [
                'group' => 'sttngs',
                'name' => 'role'
            ],
        ];
        $actions = ['create', 'read', 'update', 'delete'];
        
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permission = sprintf("client_%s_%s %s", $action, $module['group'], $module['name']);
                PermissionRecordService::create($permission);
            }
        }
    }
}
