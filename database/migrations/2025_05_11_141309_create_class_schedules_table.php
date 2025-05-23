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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            // Kelas
            $table->foreignId('class_id')
                ->constrained('classes')
                ->onDelete('cascade');

            // Penugasan guru→mapel→tahun
            $table->foreignId('assignment_id')
                ->constrained('teaching_assignments')
                ->onDelete('cascade');

            // Hari (1=Senin … 6=Sabtu atau 7=Minggu)
            $table->unsignedTinyInteger('day_of_week')->checkBetween(1, 7);

            // Jam/sesi
            $table->foreignId('hour_id')
                ->constrained('hours')
                ->onDelete('cascade');
            $table->timestamps();

            // Ini cma bisa satu kelas hanya satu jadwal per hari per jam
            $table->unique(
                ['class_id', 'day_of_week', 'hour_id'],
                'unique_class_day_hour'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
