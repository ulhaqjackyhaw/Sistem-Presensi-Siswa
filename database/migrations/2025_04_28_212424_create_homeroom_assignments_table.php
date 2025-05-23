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
        Schema::create('homeroom_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->onDelete('cascade');
            $table->foreignId('class_id')
                ->constrained('classes')
                ->onDelete('cascade');
            $table->char('teacher_id', 18);
            $table->foreign('teacher_id')
                ->references('nip')
                ->on('teachers')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['academic_year_id', 'class_id'], 'unique_homeroom_per_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homeroom_assignments');
    }
};
