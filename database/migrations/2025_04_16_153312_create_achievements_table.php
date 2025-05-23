<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->char('student_id', 18);
            $table->string('achievements_name');
            $table->string('jenis_prestasi');
            $table->string('kategori_prestasi');
            $table->date('achievement_date');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->text('description');
            $table->char('awarded_by', 18)->nullable();
            $table->string('evidence')->nullable();
            $table->enum('status', ['pending', 'processed', 'completed', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
            $table->foreign('awarded_by')->references('nip')->on('teachers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('achievements');
    }
};
