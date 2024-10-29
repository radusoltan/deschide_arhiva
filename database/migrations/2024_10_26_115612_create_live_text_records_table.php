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
        Schema::create('live_text_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->timestamp('published_at')->nullable();
            $table->text('tg_embed')->nullable();
            $table->foreignId('live_text_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_text_records');
    }
};
