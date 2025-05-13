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
                $table->foreignId('creator_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            }
            if (!Schema::hasColumn('schedules', 'participant_id')) {
                $table->foreignId('participant_id')->constrained('mentor_infos', 'mentor_no')->onDelete('cascade')->onUpdate('cascade');
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
