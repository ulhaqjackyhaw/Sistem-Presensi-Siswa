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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')
                ->constrained('class_schedules')
                ->onDelete('cascade');

            $table->char('student_id', 10);
            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');

            $table->date('meeting_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['Hadir', 'Abses', 'Sakit', 'Izin', 'Terlambat'])
                ->default('Hadir');

            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->unique(
                ['class_schedule_id', 'student_id', 'meeting_date'],
                'unique_attendance_per_meeting'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
