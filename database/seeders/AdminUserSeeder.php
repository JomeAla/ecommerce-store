<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::create([
            'name' => 'Store Admin',
            'email' => 'admin@estore.com',
            'password' => 'admin123',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}