<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'MTH01',
                'name' => 'Matematika',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran Matematika',
                'is_active' => 1
            ],
            [
                'code' => 'BIO02',
                'name' => 'Biologi',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran tentang makhluk hidup',
                'is_active' => 1
            ],
            [
                'code' => 'FIS03',
                'name' => 'Fisika',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran tentang hukum-hukum alam',
                'is_active' => 1
            ],
            [
                'code' => 'KIM04',
                'name' => 'Kimia',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran tentang zat dan reaksinya',
                'is_active' => 1
            ],
            [
                'code' => 'IND05',
                'name' => 'Bahasa Indonesia',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran Bahasa Indonesia',
                'is_active' => 1
            ],
            [
                'code' => 'ING06',
                'name' => 'Bahasa Inggris',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran Bahasa Inggris',
                'is_active' => 1
            ],
            [
                'code' => 'SEJ07',
                'name' => 'Sejarah',
                'curriculum_name' => 'Kurikulum Merdeka',
                'description' => 'Pelajaran sejarah nasional dan dunia',
                'is_active' => 1
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }
    }
}