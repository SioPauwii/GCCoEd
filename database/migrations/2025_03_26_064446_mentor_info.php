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
            $table->foreignId('ment_inf_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phoneNum')->nullable();
            $table->string('address');
            $table->string('image')->nullable();
            $table->string('course');
            $table->string('year');
            $table->json('subjects')->nullable();
            $table->string('proficiency');
            $table->string('learn_modality');
            $table->string('teach_sty');
            $table->json('availability')->nullable();
            $table->string('prefSessDur')->nullable();
            $table->text('bio');
            $table->text('exp');
            $table->longtext('credentials')->nullable();
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
