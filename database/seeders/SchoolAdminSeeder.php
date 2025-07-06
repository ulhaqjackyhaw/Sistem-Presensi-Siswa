<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolAdminMenu = Menu::create([
            'nama_menu' => 'Menu Manajemen',
            'url' => '#',
            'icon' => '',
            'parent_id' => '0',
            'urutan' => 1
        ]);

        $dashboardMenu = Menu::create([
            'nama_menu' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-home',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 1
        ]);

        $schoolAdminSubMenu = Menu::create([
            'nama_menu' => 'Kelola Data Sekolah',
            'url' => '#',
            'icon' => 'fas fa-school',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 2
        ]);

        $manajemenKurikulumMenu = Menu::create([
            'nama_menu' => 'Manajemen Kurikulum',
            'url' => 'manage-curriculums',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 1
        ]);

        Permission::create(['name' => 'create_curriculums', 'menu_id' => $manajemenKurikulumMenu->id]);
        Permission::create(['name' => 'read_curriculums', 'menu_id' => $manajemenKurikulumMenu->id]);
        Permission::create(['name' => 'update_curriculums', 'menu_id' => $manajemenKurikulumMenu->id]);
        Permission::create(['name' => 'delete_curriculums', 'menu_id' => $manajemenKurikulumMenu->id]);

        $manajemenThnAkademikMenu = Menu::create([
            'nama_menu' => 'Manajemen Thn. Akademik',
            'url' => 'manage-academic-years',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 2
        ]);

        Permission::create(['name' => 'create_academic_year', 'menu_id' => $manajemenThnAkademikMenu->id]);
        Permission::create(['name' => 'read_academic_year', 'menu_id' => $manajemenThnAkademikMenu->id]);
        Permission::create(['name' => 'update_academic_year', 'menu_id' => $manajemenThnAkademikMenu->id]);
        Permission::create(['name' => 'delete_academic_year', 'menu_id' => $manajemenThnAkademikMenu->id]);

        $manajemenDataGuruMenu = Menu::create([
            'nama_menu' => 'Manajemen Data Guru',
            'url' => 'manage-teachers',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 3
        ]);

        Permission::create(['name' => 'create_teacher', 'menu_id' => $manajemenDataGuruMenu->id]);
        Permission::create(['name' => 'read_teacher', 'menu_id' => $manajemenDataGuruMenu->id]);
        Permission::create(['name' => 'update_teacher', 'menu_id' => $manajemenDataGuruMenu->id]);
        Permission::create(['name' => 'delete_teacher', 'menu_id' => $manajemenDataGuruMenu->id]);

        $manajemenDataKelasMenu = Menu::create([
            'nama_menu' => 'Manajemen Data Kelas',
            'url' => 'manage-classes',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 4
        ]);

        Permission::create(['name' => 'create_class', 'menu_id' => $manajemenDataKelasMenu->id]);
        Permission::create(['name' => 'read_class', 'menu_id' => $manajemenDataKelasMenu->id]);
        Permission::create(['name' => 'update_class', 'menu_id' => $manajemenDataKelasMenu->id]);
        Permission::create(['name' => 'delete_class', 'menu_id' => $manajemenDataKelasMenu->id]);

        $manajemenDataMapelMenu = Menu::create([
            'nama_menu' => 'Manajemen Data Mapel',
            'url' => 'manage-subjects',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 5
        ]);

        Permission::create(['name' => 'create_subject', 'menu_id' => $manajemenDataMapelMenu->id]);
        Permission::create(['name' => 'read_subject', 'menu_id' => $manajemenDataMapelMenu->id]);
        Permission::create(['name' => 'update_subject', 'menu_id' => $manajemenDataMapelMenu->id]);
        Permission::create(['name' => 'delete_subject', 'menu_id' => $manajemenDataMapelMenu->id]);


        $manajemenDataSiswaMenu = Menu::create([
            'nama_menu' => 'Manajemen Data Siswa',
            'url' => 'manage-students',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 6
        ]);

        Permission::create(['name' => 'create_student', 'menu_id' => $manajemenDataSiswaMenu->id]);
        Permission::create(['name' => 'read_student', 'menu_id' => $manajemenDataSiswaMenu->id]);
        Permission::create(['name' => 'update_student', 'menu_id' => $manajemenDataSiswaMenu->id]);
        Permission::create(['name' => 'delete_student', 'menu_id' => $manajemenDataSiswaMenu->id]);

        $plottingGuruMapelMenu = Menu::create([
            'nama_menu' => 'Plotting Guru ke Mapel',
            'url' => 'manage-teacher-subject-assignments',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 7
        ]);

        Permission::create(['name' => 'create_teacher_subject_assignment', 'menu_id' => $plottingGuruMapelMenu->id]);
        Permission::create(['name' => 'read_teacher_subject_assignment', 'menu_id' => $plottingGuruMapelMenu->id]);
        Permission::create(['name' => 'update_teacher_subject_assignment', 'menu_id' => $plottingGuruMapelMenu->id]);
        Permission::create(['name' => 'delete_teacher_subject_assignment', 'menu_id' => $plottingGuruMapelMenu->id]);

        $manajemenJamPelajaranMenu = Menu::create([
            'nama_menu' => 'Manajemen Jam Pelajaran',
            'url' => 'manage-hours',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 8
        ]);

        Permission::create(['name' => 'create_hours', 'menu_id' => $manajemenJamPelajaranMenu->id]);
        Permission::create(['name' => 'read_hours', 'menu_id' => $manajemenJamPelajaranMenu->id]);
        Permission::create(['name' => 'update_hours', 'menu_id' => $manajemenJamPelajaranMenu->id]);
        Permission::create(['name' => 'delete_hours', 'menu_id' => $manajemenJamPelajaranMenu->id]);

        // Permissions for student class assignment, now directly under 'Kelola Data Sekolah'
        Permission::create(['name' => 'create_student_class_assignment', 'menu_id' => $schoolAdminSubMenu->id]);
        Permission::create(['name' => 'read_student_class_assignment', 'menu_id' => $schoolAdminSubMenu->id]);
        Permission::create(['name' => 'update_student_class_assignment', 'menu_id' => $schoolAdminSubMenu->id]);
        Permission::create(['name' => 'delete_student_class_assignment', 'menu_id' => $schoolAdminSubMenu->id]);


        $manajemenJadwalMenu = Menu::create([
            'nama_menu' => 'Manajemen Jadwal',
            'url' => 'manage-schedules',
            'icon' => 'fas fa-calendar-alt',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 3
        ]);

        Permission::create(['name' => 'create_schedules', 'menu_id' => $manajemenJadwalMenu->id]);
        Permission::create(['name' => 'read_schedules', 'menu_id' => $manajemenJadwalMenu->id]);
        Permission::create(['name' => 'update_schedules', 'menu_id' => $manajemenJadwalMenu->id]);
        Permission::create(['name' => 'delete_schedules', 'menu_id' => $manajemenJadwalMenu->id]);

        // Clear existing role_has_menus for role_id 2 to prevent duplicates or incorrect entries
        DB::table('role_has_menus')->where('role_id', 2)->delete();

        $menusForRole2 = [
            $schoolAdminMenu->id,
            $dashboardMenu->id,
            $schoolAdminSubMenu->id, // Parent for sub-menus
            $manajemenKurikulumMenu->id,
            $manajemenThnAkademikMenu->id,
            $manajemenDataGuruMenu->id,
            $manajemenDataKelasMenu->id,
            $manajemenDataMapelMenu->id,
            $manajemenDataSiswaMenu->id,
            $plottingGuruMapelMenu->id,
            $manajemenJamPelajaranMenu->id,
            $manajemenJadwalMenu->id,
        ];

        foreach ($menusForRole2 as $menuId) {
            DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [$menuId, 2]);
        }

        User::factory()->create([
            'name' => 'Admin Sekolah 1',
            'email' => 'adminsekolah1@gmail.com',
            'password' => Hash::make("4dm1nS3k0l4h_879867yhdaad89u"),
        ]);

        $schoolAdmin = Role::create(['name' => 'Admin Sekolah']);
        $schoolAdmin->givePermissionTo([
            'create_curriculums',
            'read_curriculums',
            'update_curriculums',
            'delete_curriculums',
            'create_academic_year',
            'read_academic_year',
            'update_academic_year',
            'delete_academic_year',
            'create_class',
            'read_class',
            'update_class',
            'delete_class',
            'create_subject',
            'read_subject',
            'update_subject',
            'delete_subject',
            'create_teacher',
            'read_teacher',
            'update_teacher',
            'delete_teacher',
            'create_student',
            'read_student',
            'update_student',
            'delete_student',
            'create_hours',
            'read_hours',
            'update_hours',
            'delete_hours',
            'create_schedules',
            'read_schedules',
            'update_schedules',
            'delete_schedules',
            'create_student_class_assignment',
            'read_student_class_assignment',
            'update_student_class_assignment',
            'delete_student_class_assignment',
            'create_teacher_subject_assignment',
            'read_teacher_subject_assignment',
            'update_teacher_subject_assignment',
            'delete_teacher_subject_assignment',
        ]);
        User::firstWhere('email', 'adminsekolah1@gmail.com')->assignRole('Admin Sekolah');
    }
}
