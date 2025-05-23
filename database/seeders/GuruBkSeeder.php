<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use App\Models\AchievementPoint;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruBkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role Guru BK
        $guruBkRole = Role::create(['name' => 'Guru BK']);

        // Buat user Guru BK
        $guruBkUser = User::create([
            'name' => 'Guru BK 1',
            'email' => 'gurubk1@gmail.com',
            'password' => Hash::make('gurubk123'),
        ]);
        $guruBkUser->assignRole('Guru BK');

        // Buat menu utama
        $mainMenu = Menu::create([
            'nama_menu' => 'Menu Manajemen Guru BK',
            'url' => '#',
            'icon' => '',
            'parent_id' => '0',
            'urutan' => 1
        ]);

        // // Dashboard
        // $dashboardMenu = Menu::create([
        //     'nama_menu' => 'Dashboard',
        //     'url' => 'home',
        //     'icon' => 'fas fa-home',
        //     'parent_id' => $mainMenu->id,
        //     'urutan' => 1
        // ]);

        // Pelanggaran
        $pelanggaranMenu = Menu::create([
            'nama_menu' => 'Pelanggaran',
            'url' => '#',
            'icon' => 'fas fa-school',
            'parent_id' => $mainMenu->id,
            'urutan' => 1
        ]);
        $kelolaPelanggaran = Menu::create([
            'nama_menu' => 'Kelola Pelanggaran',
            'url' => 'violation-management',
            'icon' => '',
            'parent_id' => $pelanggaranMenu->id,
            'urutan' => 1
        ]);
        $laporanPelanggaran = Menu::create([
            'nama_menu' => 'Laporan Pelanggaran',
            'url' => 'violation-report',
            'icon' => '',
            'parent_id' => $pelanggaranMenu->id,
            'urutan' => 2
        ]);

        // Prestasi
        $prestasiMenu = Menu::create([
            'nama_menu' => 'Prestasi',
            'url' => '#',
            'icon' => 'fas fa-star',
            'parent_id' => $mainMenu->id,
            'urutan' => 2
        ]);
        $kelolaPrestasi = Menu::create([
            'nama_menu' => 'Kelola Prestasi',
            'url' => 'achievement-management',
            'icon' => '',
            'parent_id' => $prestasiMenu->id,
            'urutan' => 1
        ]);
        $laporanPrestasi = Menu::create([
            'nama_menu' => 'Laporan Prestasi',
            'url' => 'achievement-report',
            'icon' => '',
            'parent_id' => $prestasiMenu->id,
            'urutan' => 2
        ]);

        // Buat permission CRUD untuk setiap child menu
        $permissions = [];
        $childMenus = [
            'kelola_pelanggaran' => $kelolaPelanggaran->id,
            'laporan_pelanggaran' => $laporanPelanggaran->id,
            'kelola_prestasi' => $kelolaPrestasi->id,
            'laporan_prestasi' => $laporanPrestasi->id,
        ];
        foreach ($childMenus as $key => $menuId) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $perm = Permission::create([
                    'name' => $action . '_' . $key,
                    'menu_id' => $menuId
                ]);
                $permissions[] = $perm->name;
            }
        }
        // Assign semua permission ke role Guru BK
        $guruBkRole->givePermissionTo($permissions);

        // Assign menu ke role Guru BK (role_id didapat dari $guruBkRole)
        DB::table('role_has_menus')->insert([
            ['menu_id' => $mainMenu->id, 'role_id' => $guruBkRole->id],
            // ['menu_id' => $dashboardMenu->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $pelanggaranMenu->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $kelolaPelanggaran->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $laporanPelanggaran->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $prestasiMenu->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $kelolaPrestasi->id, 'role_id' => $guruBkRole->id],
            ['menu_id' => $laporanPrestasi->id, 'role_id' => $guruBkRole->id],
        ]);
    }
}
