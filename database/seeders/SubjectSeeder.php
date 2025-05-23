<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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
                'code' => 'MTH',
                'name' => 'Matematika',
                'description' => 'Pelajaran Matematika',
                'is_active' => 1
            ],
            [
                'code' => 'BIO',
                'name' => 'Biologi',
                'description' => 'Pelajaran tentang makhluk hidup',
                'is_active' => 1
            ],
            [
                'code' => 'FIS',
                'name' => 'Fisika',
                'description' => 'Pelajaran tentang hukum-hukum alam',
                'is_active' => 1
            ],
            [
                'code' => 'KIM',
                'name' => 'Kimia',
                'description' => 'Pelajaran tentang zat dan reaksinya',
                'is_active' => 1
            ],
            [
                'code' => 'IND',
                'name' => 'Bahasa Indonesia',
                'description' => 'Pelajaran Bahasa Indonesia',
                'is_active' => 1
            ],
            [
                'code' => 'ING',
                'name' => 'Bahasa Inggris',
                'description' => 'Pelajaran Bahasa Inggris',
                'is_active' => 1
            ],
            [
                'code' => 'SEJ',
                'name' => 'Sejarah',
                'description' => 'Pelajaran sejarah nasional dan dunia',
                'is_active' => 1
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }
    }
}