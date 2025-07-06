<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->timestamp('viewed_at')->nullable()->after('validated_at');
        });
    }

    public function down()
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn('viewed_at');
        });
    }
};
