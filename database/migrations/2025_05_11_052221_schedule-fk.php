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
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'creator_id')) {
                $table->unsignedInteger('creator_id')->after('id');
                $table->foreign('creator_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            }
            if (!Schema::hasColumn('schedules', 'participant_id')) {
                $table->unsignedBigInteger('participant_id')->after('creator_id');
                $table->foreign('participant_id')
                      ->references('mentor_no')
                      ->on('mentor_infos')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['participant_id']);
            $table->dropColumn(['creator_id', 'participant_id']);
        });
    }
};
