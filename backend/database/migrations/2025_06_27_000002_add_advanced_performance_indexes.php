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
        // Add composite indexes for better query performance
        Schema::table('enrollments', function (Blueprint $table) {
            // Composite index for user enrollments with timestamps
            $table->index(['user_id', 'created_at'], 'enrollments_user_created_idx');
            $table->index(['course_id', 'created_at'], 'enrollments_course_created_idx');
            
            // Index for enrollment status queries
            if (!Schema::hasColumn('enrollments', 'status')) {
                $table->string('status')->default('active')->after('enrolled_at');
            }
            $table->index(['status', 'created_at'], 'enrollments_status_created_idx');
        });

        Schema::table('purchases', function (Blueprint $table) {
            // Composite indexes for purchase queries
            $table->index(['user_id', 'status', 'created_at'], 'purchases_user_status_created_idx');
            $table->index(['course_id', 'status'], 'purchases_course_status_idx');
            
            // Index for payment tracking
            $table->index(['stripe_payment_intent_id'], 'purchases_stripe_intent_idx');
        });

        Schema::table('lessons', function (Blueprint $table) {
            // Composite index for course lessons ordering
            $table->index(['course_id', 'order'], 'lessons_course_order_idx');
            $table->index(['course_id', 'published', 'order'], 'lessons_course_published_order_idx');
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            // Check if table exists first
            if (Schema::hasTable('lesson_progress')) {
                $table->index(['user_id', 'lesson_id'], 'lesson_progress_user_lesson_idx');
                $table->index(['user_id', 'completed_at'], 'lesson_progress_user_completed_idx');
            }
        });

        // Add full-text search index for courses
        Schema::table('courses', function (Blueprint $table) {
            // MySQL/MariaDB full-text index
            if (config('database.default') === 'mysql') {
                DB::statement('ALTER TABLE courses ADD FULLTEXT fulltext_search (title, description)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_user_created_idx');
            $table->dropIndex('enrollments_course_created_idx');
            $table->dropIndex('enrollments_status_created_idx');
            if (Schema::hasColumn('enrollments', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_user_status_created_idx');
            $table->dropIndex('purchases_course_status_idx');
            $table->dropIndex('purchases_stripe_intent_idx');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_course_order_idx');
            $table->dropIndex('lessons_course_published_order_idx');
        });

        if (Schema::hasTable('lesson_progress')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->dropIndex('lesson_progress_user_lesson_idx');
                $table->dropIndex('lesson_progress_user_completed_idx');
            });
        }

        // Drop full-text index
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE courses DROP INDEX fulltext_search');
        }
    }
};
