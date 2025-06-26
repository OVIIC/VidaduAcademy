<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->foreignId('last_accessed_lesson_id')->nullable()->constrained('lessons')->onDelete('set null');
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['course_id']);
            $table->index(['progress_percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
