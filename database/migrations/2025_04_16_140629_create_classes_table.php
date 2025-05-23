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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('parallel_name');
            $table->unsignedBigInteger('academic_year_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onDelete('cascade');
            $table->unique(['academic_year_id', 'name', 'parallel_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
