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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code')->unique(); // contoh: MAT01
            $table->string('subject_name');          // contoh: Matematika
            $table->string('curriculum_name');// nama kurikulum sebagai string
             $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // jika nanti diaktifkan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};