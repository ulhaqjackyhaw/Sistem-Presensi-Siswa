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
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->unsignedBigInteger('assignment_id')->nullable()->change();
            $table->foreign('assignment_id')->references('id')->on('teaching_assignments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->unsignedBigInteger('assignment_id')->nullable(false)->change();
            $table->foreign('assignment_id')->references('id')->on('teaching_assignments')->onDelete('cascade');
        });
    }
};
