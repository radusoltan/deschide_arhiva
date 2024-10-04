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
        Schema::create('author_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')
                ->references('id')
                ->on('authors')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->fulltext();
            $table->string('slug');
            $table->unique(['locale','slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_translations');
    }
};
