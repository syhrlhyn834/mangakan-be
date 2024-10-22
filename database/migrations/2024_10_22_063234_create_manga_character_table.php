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
        Schema::create('manga_character', function (Blueprint $table) {
            $table->unsignedBigInteger('manga_id');
            $table->unsignedBigInteger('character_id');
            $table->foreign('manga_id')->references('id')->on('mangas')->onDelete('cascade');
            $table->foreign('character_id')->references('id')->on('characters')->onDelete('cascade');
            $table->primary(['manga_id', 'character_id']); // Composite key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_character');
    }
};
