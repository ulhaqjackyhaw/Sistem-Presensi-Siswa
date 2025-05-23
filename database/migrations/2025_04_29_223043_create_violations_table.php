<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->char('student_id', 18);
            $table->foreignId('violation_points_id')->constrained('violation_points')->onDelete('cascade');
            $table->date('violation_date');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->text('description');
            $table->string('penalty')->nullable();
            $table->char('reported_by', 18)->nullable();
            $table->string('evidence')->nullable();
            $table->enum('status', ['pending', 'processed', 'completed'])->default('pending');
            $table->timestamps();

            $table->foreign('student_id')->references('nisn')->on('students')->onDelete('cascade');
            $table->foreign('reported_by')->references('nip')->on('teachers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('violations');
    }
};
