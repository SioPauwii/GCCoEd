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
        Schema::create('learner_info', function (Blueprint $table) {
            $table->id('learner_no');
            $table->foreignId('learn_inf_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phoneNum')->nullable();
            $table->string('address');
            $table->string('image')->nullable();
            $table->string('course');
            $table->string('year');
            $table->json('subjects')->nullable();
            $table->string('learn-modality');
            $table->string('learn-sty');    
            $table->json('availability')->nullable();
            $table->string('prefSessDur')->nullable();
            $table->text('bio');
            $table->text('goals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_info');
    }
};
