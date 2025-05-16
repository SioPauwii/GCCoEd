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
            $table->unsignedInteger('learn_inf_id');
            $table->foreign('learn_inf_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('phoneNum')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('course')->nullable();
            $table->string('year')->nullable();
            $table->json('subjects')->nullable();
            $table->string('learn_modality')->nullable();
            $table->string('learn_sty')->nullable();    
            $table->json('availability')->nullable();
            $table->string('prefSessDur')->nullable();
            $table->text('bio')->nullable();
            $table->text('goals')->nullable();
            $table->string('account_status')->default('OK');
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
