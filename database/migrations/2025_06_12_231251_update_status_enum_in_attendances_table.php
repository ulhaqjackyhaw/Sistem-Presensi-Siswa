<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Absen', 'Sakit', 'Izin', 'Terlambat'])
                ->default('Hadir')
                ->after('time_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Abses', 'Sakit', 'Izin', 'Terlambat'])
                ->default('Hadir')
                ->after('time_out');
        });
    }
};
