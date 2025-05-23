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

        Menu::create([
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

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Kurikulum',
            'url' => 'manage-curriculums',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 1
        ]);

        Permission::create(['name' => 'create_curriculums', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_curriculums', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_curriculums', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_curriculums', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Thn. Akademik',
            'url' => 'manage-academic-years',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 2
        ]);

        Permission::create(['name' => 'create_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_academic_year', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Kelas',
            'url' => 'manage-classes',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 3
        ]);

        Permission::create(['name' => 'create_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_class', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Mapel',
            'url' => 'manage-subjects',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 4
        ]);

        Permission::create(['name' => 'create_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_subject', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Guru',
            'url' => 'manage-teachers',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 5
        ]);

        Permission::create(['name' => 'create_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_teacher', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Siswa',
            'url' => 'manage-students',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 6
        ]);

        Permission::create(['name' => 'create_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_student', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Jam Pelajaran',
            'url' => 'manage-hours',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 7
        ]);

        Permission::create(['name' => 'create_hours', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_hours', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_hours', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_hours', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Jadwal',
            'url' => 'manage-schedules',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 8
        ]);

        Permission::create(['name' => 'create_schedules', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_schedules', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_schedules', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_schedules', 'menu_id' => $subMenuId->id]);

        $schoolAdminSubMenu = Menu::create([
            'nama_menu' => 'Manajemen Plotting',
            'url' => '#',
            'icon' => 'fas fa-project-diagram',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 3
        ]);

        // $subMenuId = Menu::create([
        //     'nama_menu' => 'Plotting Wali Kelas',
        //     'url' => 'manage-homeroom-assignments',
        //     'parent_id' => $schoolAdminSubMenu->id,
        //     'urutan' => 1
        // ]);

        // Permission::create(['name' => 'create_homeroom_assignment', 'menu_id' => $subMenuId->id]);
        // Permission::create(['name' => 'read_homeroom_assignment', 'menu_id' => $subMenuId->id]);
        // Permission::create(['name' => 'update_homeroom_assignment', 'menu_id' => $subMenuId->id]);
        // Permission::create(['name' => 'delete_homeroom_assignment', 'menu_id' => $subMenuId->id]);

        // $subMenuId = Menu::create([
        //     'nama_menu' => 'Plotting Siswa ke Kelas',
        //     'url' => 'manage-student-class-assignments',
        //     'parent_id' => $schoolAdminSubMenu->id,
        //     'urutan' => 2
        // ]);

        Permission::create(['name' => 'create_student_class_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_student_class_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_student_class_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_student_class_assignment', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Plotting Guru ke Mapel',
            'url' => 'manage-teacher-subject-assignments',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 3
        ]);

        Permission::create(['name' => 'create_teacher_subject_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_teacher_subject_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_teacher_subject_assignment', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_teacher_subject_assignment', 'menu_id' => $subMenuId->id]);

        $schoolAdminSubMenu = Menu::create([
            'nama_menu' => 'Menu Presensi',
            'url' => '#',
            'icon' => 'fas fa-expand',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 4
        ]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Presensi Siswa',
            'url' => 'manage-attendances',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 1
        ]);

        Permission::create(['name' => 'create_attendances', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_attendances', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_attendances', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_attendances', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Riwayat Presensi',
            'url' => 'manage-attendance-history',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 2
        ]);

        Permission::create(['name' => 'create_attendance_history', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_attendance_history', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_attendance_history', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_attendance_history', 'menu_id' => $subMenuId->id]);

        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [9, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [10, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [11, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [12, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [13, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [14, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [15, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [16, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [17, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [18, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [19, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [20, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [21, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [22, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [23, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [24, 2]);
        // DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [25, 2]);
        // DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [26, 2]);

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
            // 'create_homeroom_assignment',
            // 'read_homeroom_assignment',
            // 'update_homeroom_assignment',
            // 'delete_homeroom_assignment',
            'create_student_class_assignment',
            'read_student_class_assignment',
            'update_student_class_assignment',
            'delete_student_class_assignment',
            'create_teacher_subject_assignment',
            'read_teacher_subject_assignment',
            'update_teacher_subject_assignment',
            'delete_teacher_subject_assignment',
            'create_attendances',
            'read_attendances',
            'update_attendances',
            'delete_attendances',
            'create_attendance_history',
            'read_attendance_history',
            'update_attendance_history',
            'delete_attendance_history',

        ]);
        User::firstWhere('email', 'adminsekolah1@gmail.com')->assignRole('Admin Sekolah');
    }
}
