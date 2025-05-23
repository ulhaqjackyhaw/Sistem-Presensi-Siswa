<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat role untuk siswa dengan guard 'web'
        Role::firstOrCreate(['name' => 'Siswa', 'guard_name' => 'web']);
    }
}