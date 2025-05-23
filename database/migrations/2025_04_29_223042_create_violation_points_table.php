<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViolationPointsTable extends Migration
{
    public function up()
    {
        Schema::create('violation_points', function (Blueprint $table) {
            $table->id();
            $table->string('violation_type');
            $table->integer('points');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('violation_points');
    }
}
