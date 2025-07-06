<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role Siswa ada
        $siswaRole = Role::firstOrCreate([
            'name' => 'Siswa',
            'guard_name' => 'web',
        ]);

        // Menu utama
        $mainMenu = Menu::firstOrCreate([
            'nama_menu' => 'Menu Siswa',
            'url' => '#',
            'icon' => '',
            'parent_id' => 0,
            'urutan' => 1,
        ]);

        // Menu Dashboard
        $dashboardMenu = Menu::firstOrCreate([
            'nama_menu' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-home',
            'parent_id' => $mainMenu->id,
            'urutan' => 1,
        ]);

        // Menu Riwayat Presensi
        $attendanceMenu = Menu::firstOrCreate(
            [
                'nama_menu' => 'Riwayat Presensi',
                'url' => 'manage-attendance-students',
                'parent_id' => $mainMenu->id,
            ],
            [
                'icon' => 'fas fa-expand',
                'urutan' => 2,
            ]
        );

        $pointMenu = Menu::create([
            'nama_menu' => 'Poin Siswa',
            'url' => '#',
            'icon' => 'fas fa-exclamation-circle',
            'parent_id' => $mainMenu->id,
            'urutan' => 3
        ]);

        $violationPointMenu = Menu::create([
            'nama_menu' => 'Poin Pelanggaran',
            'url' => '',
            'parent_id' => $pointMenu->id,
            'urutan' => 1
        ]);

        $achievementPointMenu = Menu::create([
            'nama_menu' => 'Poin Prestasi',
            'url' => '',
            'parent_id' => $pointMenu->id,
            'urutan' => 2
        ]);

        // Hubungkan menu ke role Siswa
        DB::table('role_has_menus')->insertOrIgnore([
            ['menu_id' => $mainMenu->id, 'role_id' => $siswaRole->id],
            ['menu_id' => $dashboardMenu->id, 'role_id' => $siswaRole->id],
            ['menu_id' => $attendanceMenu->id, 'role_id' => $siswaRole->id],
            ['menu_id' => $pointMenu->id, 'role_id' => $siswaRole->id],
            ['menu_id' => $violationPointMenu->id, 'role_id' => $siswaRole->id],
            ['menu_id' => $achievementPointMenu->id, 'role_id' => $siswaRole->id],
        ]);
    }
}
