<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // DROP FOREIGN KEY di semua tabel terkait sebelum ubah kolom

        if ($this->foreignKeyExists('student_class_assignments', 'student_class_assignments_student_id_foreign')) {
            Schema::table('student_class_assignments', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('achievements', 'achievements_student_id_foreign')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('violations', 'violations_student_id_foreign')) {
            Schema::table('violations', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('attendances', 'attendances_student_id_foreign')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        // UBAH PANJANG NISN DI TABLE STUDENTS
        Schema::table('students', function (Blueprint $table) {
            $table->char('nisn', 10)->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        // UBAH PANJANG student_id DI TABLE student_class_assignments
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->char('student_id', 10)->change();
        });

        // UBAH PANJANG student_id DI TABLE achievements
        Schema::table('achievements', function (Blueprint $table) {
            $table->char('student_id', 10)->change();
        });

        // UBAH PANJANG student_id DI TABLE violations
        Schema::table('violations', function (Blueprint $table) {
            $table->char('student_id', 10)->change();
        });

        // RE-ADD FOREIGN KEY DENGAN PANJANG BARU
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // DROP FOREIGN KEY di semua tabel terkait sebelum rollback perubahan

        if ($this->foreignKeyExists('student_class_assignments', 'student_class_assignments_student_id_foreign')) {
            Schema::table('student_class_assignments', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('achievements', 'achievements_student_id_foreign')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('violations', 'violations_student_id_foreign')) {
            Schema::table('violations', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if ($this->foreignKeyExists('attendances', 'attendances_student_id_foreign')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        // BALIKKAN PANJANG NISN DI TABLE STUDENTS
        Schema::table('students', function (Blueprint $table) {
            $table->char('nisn', 18)->change();
        });

        // BALIKKAN PANJANG student_id DI TABLE student_class_assignments
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->char('student_id', 18)->change();
        });

        // BALIKKAN PANJANG student_id DI TABLE achievements
        Schema::table('achievements', function (Blueprint $table) {
            $table->char('student_id', 18)->change();
        });

        // BALIKKAN PANJANG student_id DI TABLE violations
        Schema::table('violations', function (Blueprint $table) {
            $table->char('student_id', 18)->change();
        });

        // BALIKKAN PANJANG student_id DI TABLE attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->char('student_id', 18)->change();
        });

        // RE-ADD FOREIGN KEY LAMA
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Helper untuk cek apakah foreign key ada di tabel.
     */
    protected function foreignKeyExists(string $tableName, string $foreignKeyName): bool
    {
        $result = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'
             AND TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?",
            [$tableName, $foreignKeyName]
        );

        return count($result) > 0;
    }
};
