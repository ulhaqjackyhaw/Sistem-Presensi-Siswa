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
        Schema::create('students', function (Blueprint $table) {
            $table->char('nisn', 18)->primary();
            $table->unsignedBigInteger('class_id');
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date');
            $table->text('address')->nullable();

            $table->string('phone')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('parent_email')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
