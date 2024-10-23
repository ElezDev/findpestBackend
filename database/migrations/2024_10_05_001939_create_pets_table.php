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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('breed');
            $table->string('size');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('location');
            $table->enum('adoption_status', ['available', 'adopted', 'in_process']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('latitude');  
            $table->string('longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
