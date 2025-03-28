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
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->text('comment')->nullable()->comment('Optional Feedback Message');
            $table->tinyInteger('rating')->unsigned()->comment('Rating from 1 to 5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feedback');
    }
};
