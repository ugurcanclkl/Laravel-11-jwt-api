<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Yajra\Acl\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email'             => 'developer@mail.com',
            'password'          => Hash::make('asdfasdf'),
            'name'              => 'Developer',
        ])->attachRoleBySlug('admin');



        User::create([
            'email'    => 'user@mail.com',
            'password' => Hash::make('asdfasdf'),
            'name'     => 'User',
        ])->attachRoleBySlug('user');

        User::create([
            'email' => 'guest@mail.com',
            'password' => Hash::make('asdfasdf'),
            'name' => 'Guest',
        ])->attachRoleBySlug('guest');
    }
}
