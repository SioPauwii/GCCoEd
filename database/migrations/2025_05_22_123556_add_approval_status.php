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
        Schema::table('mentor_infos', function (Blueprint $table) {
            $table->enum('approval_status', ['approved', 'pending', 'rejected'])
                  ->default('pending')
                  ->after('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_infos', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
