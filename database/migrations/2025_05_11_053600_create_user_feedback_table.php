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
        if (!Schema::hasTable('user_feedback')) {
            Schema::create('user_feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
                $table->foreignId('reviewee_id')->constrained('mentor_infos', 'mentor_no')->onDelete('cascade')->onUpdate('cascade');
                $table->text('feedback');
                $table->integer('rating');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feedback');
    }
};
