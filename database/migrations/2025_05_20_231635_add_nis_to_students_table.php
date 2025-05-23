<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nis', 10)->after('nisn');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'nis')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('nis');
            });
        }
    }
};
