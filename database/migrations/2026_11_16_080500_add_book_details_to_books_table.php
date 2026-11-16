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
        Schema::table('books', function (Blueprint $table) {
            $table->string('title', 150)->nullable();
            $table->string('author', 100)->nullable();
            $table->string('genre', 50)->nullable();
            $table->string('cover', 2048)->nullable();
            $table->foreignId('publisher_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['publisher_id']);
            $table->dropColumn(['title', 'author', 'genre', 'cover', 'publisher_id']);
        });
    }
};
