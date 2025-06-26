<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('video_duration')->default(0); // in seconds
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->longText('transcript')->nullable();
            $table->json('resources')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
