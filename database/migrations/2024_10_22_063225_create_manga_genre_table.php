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
        Schema::create('manga_genre', function (Blueprint $table) {
            $table->unsignedBigInteger('manga_id');
            $table->unsignedBigInteger('genre_id');
            $table->foreign('manga_id')->references('id')->on('mangas')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
            $table->primary(['manga_id', 'genre_id']); // Composite key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_genre');
    }
};
