<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'phone' => '08123456789',
            'email' => 'admin@aksamedia.com',
            'password' => bcrypt('pastibisa'), // Jangan lupa di-hash!
        ]);
    }
}
