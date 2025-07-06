<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchievementPointsTable extends Migration
{
    public function up()
    {
        Schema::create('achievement_points', function (Blueprint $table) {
            $table->id();
            $table->string('achievement_type');
            $table->string('achievement_category');
            $table->integer('points');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('achievement_points');
    }
}
