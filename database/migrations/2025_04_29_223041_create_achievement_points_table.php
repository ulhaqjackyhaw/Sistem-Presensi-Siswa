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
            $table->string('jenis_prestasi');
            $table->string('kategori_prestasi');
            $table->integer('poin');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('achievement_points');
    }
}
