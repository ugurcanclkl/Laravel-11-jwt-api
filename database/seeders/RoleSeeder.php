<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Yajra\Acl\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'user',
                'slug' => 'user',
            ],
            [
                'name' => 'guest',
                'slug' => 'guest',
            ],
        ]);
    }
}
