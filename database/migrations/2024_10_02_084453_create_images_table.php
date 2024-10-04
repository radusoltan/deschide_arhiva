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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->integer('width');
            $table->integer('height');
            $table->string('source')->nullable(true)->default((null));
            $table->string('author')->nullable(true)->default(null);
            $table->text('description')->nullable(true)->default(null);
            $table->unique(['name']);
            $table->integer('old_number')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
