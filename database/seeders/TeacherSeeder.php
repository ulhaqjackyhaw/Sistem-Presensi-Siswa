<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

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

        // Menu Lapor Prestasi (langsung ke form)
        $laporPrestasi = Menu::create([
            'nama_menu' => 'Lapor Prestasi',
            'url' => 'achievements',
            'icon' => 'fas fa-star',
            'parent_id' => $mainMenu->id,
            'urutan' => 2
        ]);

        // Menu Lapor Pelanggaran (langsung ke form)
        $laporPelanggaran = Menu::create([
            'nama_menu' => 'Lapor Pelanggaran',
            'url' => 'violations',
            'icon' => 'fas fa-exclamation-triangle',
            'parent_id' => $mainMenu->id,
            'urutan' => 3
        ]);

        // Assign semua menu ke role Guru
        $menuIds = [
            $mainMenu->id,
            $dashboardMenu->id,
            $laporPrestasi->id,
            $laporPelanggaran->id
        ];
        foreach ($menuIds as $menuId) {
            DB::table('role_has_menus')->insert([
                'menu_id' => $menuId,
                'role_id' => $role->id
            ]);
        }
    }
}
