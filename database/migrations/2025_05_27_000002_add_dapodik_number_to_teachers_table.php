<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('teachers', 'dapodik_number')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('dapodik_number', 16)->nullable()->after('nip');
            });
        } else {
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('dapodik_number', 16)->nullable()->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('teachers', 'dapodik_number')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('dapodik_number');
            });
        }
    }
};
