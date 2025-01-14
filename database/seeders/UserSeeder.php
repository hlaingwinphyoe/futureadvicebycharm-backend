<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = Role::create([
            'slug' => 'developer',
            'name' => 'Developer',
            'type' => 'Developer',
        ]);

        $admin = Role::create([
            'slug' => 'admin',
            'name' => 'Admin',
            'type' => 'Staff',
        ]);

        $astrologer = Role::create([
            'slug' => 'astrologer',
            'name' => 'Astrologer',
            'type' => 'Staff',
        ]);

        $role = Role::create([
            'slug' => 'user',
            'name' => 'User',
            'type' => 'Staff'
        ]);

        User::create([
            'name' => 'kiran',
            'phone' => '0944912812',
            'email' => 'dev.hlaingwinphyoe@gmail.com',
            'role_id' => $developer->id,
            'password' => Hash::make('password-123'),
        ]);

        User::create([
            'name' => 'ShinLwinThant',
            'phone' => '0660857579',
            'email' => 'shinthant234223@gmail.com',
            'role_id' => $astrologer->id,
            'password' => Hash::make('password-123'),
        ]);
    }
}
