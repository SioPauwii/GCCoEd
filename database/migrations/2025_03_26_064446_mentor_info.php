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
        Schema::create('mentor_infos', function (Blueprint $table) {
            $table->id('mentor_no');
            $table->unsignedInteger('ment_inf_id');
            $table->foreign('ment_inf_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('phoneNum')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('course')->nullable();
            $table->string('year')->nullable();
            $table->json('subjects')->nullable();
            $table->string('proficiency')->nullable();
            $table->string('learn_modality')->nullable();
            $table->string('teach_sty')->nullable();
            $table->json('availability')->nullable();
            $table->string('prefSessDur')->nullable();
            $table->text('bio')->nullable();
            $table->text('exp')->nullable();
            $table->longtext('credentials')->nullable();
            $table->string('account_status')->default('OK');
            $table->tinyInteger('approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_infos');
    }
};
