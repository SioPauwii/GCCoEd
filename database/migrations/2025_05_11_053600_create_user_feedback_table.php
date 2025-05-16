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
                $table->unsignedInteger('reviewer_id');
                $table->foreign('reviewer_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
                
                $table->unsignedBigInteger('reviewee_id');
                $table->foreign('reviewee_id')
                      ->references('mentor_no')
                      ->on('mentor_infos')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
                
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
