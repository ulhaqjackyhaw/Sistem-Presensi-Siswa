<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('violation_points', function (Blueprint $table) {
            $table->string('violation_level')->after('violation_type');
        });
    }

    public function down()
    {
        Schema::table('violation_points', function (Blueprint $table) {
            $table->dropColumn('violation_level');
        });
    }

};
