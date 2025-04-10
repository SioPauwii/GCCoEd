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
            $table->string('city-muni');
            $table->string('brgy');
            $table->string('image')->nullable();
            $table->string('course');
            $table->string('department');
            $table->string('year');
            $table->json('subjects')->nullable();
            $table->string('proficiency');
            $table->string('learn-modality');
            $table->string('teach-sty');
            $table->json('availability')->nullable();
            $table->string('prefSessDur')->nullable();
            $table->text('bio');
            $table->text('exp');
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
