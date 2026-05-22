<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@startupregistry.al'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'is_active' => true,
                'status' =>''
            ]
        );
    }
}
