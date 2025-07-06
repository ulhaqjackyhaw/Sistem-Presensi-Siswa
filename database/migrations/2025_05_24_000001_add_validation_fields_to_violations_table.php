<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->char('validator_id', 18)->nullable();
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->foreign('validator_id')
                  ->references('nip')
                  ->on('teachers')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropForeign(['validator_id']);
            $table->dropColumn(['validation_status', 'validator_id', 'validation_notes', 'validated_at']);
        });
    }
};
