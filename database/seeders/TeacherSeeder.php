<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Guru']);
        $this->createMenu($role);
    }

    public function createMenu($role)
    {
        // Buat menu utama
        $mainMenu = Menu::create([
            'nama_menu' => 'Menu Manajemen',
            'url' => '#',
            'icon' => '',
            'parent_id' => '0',
            'urutan' => 1
        ]);

        // Dashboard
        $dashboardMenu = Menu::create([
            'nama_menu' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-home',
            'parent_id' => $mainMenu->id,
            'urutan' => 1
        ]);

        // Jadwal Pelajaran
        $jadwalPelajaran = Menu::create([
            'nama_menu' => 'Jadwal Pelajaran',
            'url' => 'teacher-schedule',
            'icon' => 'fas fa-calendar-alt',
            'parent_id' => $mainMenu->id,
            'urutan' => 2
        ]);

        // Menu Lapor Prestasi (langsung ke form)
        $laporPrestasi = Menu::create([
            'nama_menu' => 'Lapor Prestasi',
            'url' => 'achievements',
            'icon' => 'fas fa-star',
            'parent_id' => $mainMenu->id,
            'urutan' => 3
        ]);

        // Menu Lapor Pelanggaran (langsung ke form)
        $laporPelanggaran = Menu::create([
            'nama_menu' => 'Lapor Pelanggaran',
            'url' => 'violations',
            'icon' => 'fas fa-exclamation-triangle',
            'parent_id' => $mainMenu->id,
            'urutan' => 4
        ]);

        $menuPresensi = Menu::create([
            'nama_menu' => 'Menu Presensi',
            'url' => '#',
            'icon' => 'fas fa-expand',
            'parent_id' => $mainMenu->id,
            'urutan' => 4
        ]);

        $presensiSiswa = Menu::create([
            'nama_menu' => 'Presensi Siswa',
            'url' => 'manage-attendances',
            'parent_id' => $menuPresensi->id,
            'urutan' => 1
        ]);

        $riwayatPresensi = Menu::create([
            'nama_menu' => 'Riwayat Presensi',
            'url' => 'manage-attendances-history',
            'parent_id' => $menuPresensi->id,
            'urutan' => 2
        ]);

        $permissions = [
            Permission::create(['name' => 'create_attendance', 'menu_id' => $presensiSiswa->id]),
            Permission::create(['name' => 'read_attendance', 'menu_id' => $presensiSiswa->id]),
            Permission::create(['name' => 'update_attendance', 'menu_id' => $presensiSiswa->id]),
            Permission::create(['name' => 'delete_attendance', 'menu_id' => $presensiSiswa->id]),
            Permission::create(['name' => 'create_attendance_history', 'menu_id' => $riwayatPresensi->id]),
            Permission::create(['name' => 'read_attendance_history', 'menu_id' => $riwayatPresensi->id]),
            Permission::create(['name' => 'update_attendance_history', 'menu_id' => $riwayatPresensi->id]),
            Permission::create(['name' => 'delete_attendance_history', 'menu_id' => $riwayatPresensi->id]),
        ];


        // Assign semua menu ke role Guru
        $menuIds = [
            $mainMenu->id,
            $dashboardMenu->id,
            $jadwalPelajaran->id,
            $laporPrestasi->id,
            $laporPelanggaran->id,
            $menuPresensi->id,
            $presensiSiswa->id,
            $riwayatPresensi->id,
        ];
        foreach ($menuIds as $menuId) {
            DB::table('role_has_menus')->insert([
                'menu_id' => $menuId,
                'role_id' => $role->id
            ]);
        }

        $role->givePermissionTo($permissions);
    }
}
