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
        Schema::table('courses', function (Blueprint $table) {
            // Check if indexes don't exist before creating
            if (!Schema::hasIndex('courses', 'courses_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'courses_status_created_at_index');
            }
            if (!Schema::hasIndex('courses', 'courses_status_featured_index')) {
                $table->index(['status', 'featured'], 'courses_status_featured_index');
            }
            if (!Schema::hasIndex('courses', 'courses_instructor_id_status_index')) {
                $table->index(['instructor_id', 'status'], 'courses_instructor_id_status_index');
            }
            if (!Schema::hasIndex('courses', 'courses_difficulty_level_status_index')) {
                $table->index(['difficulty_level', 'status'], 'courses_difficulty_level_status_index');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasIndex('enrollments', 'enrollments_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'enrollments_user_id_created_at_index');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasIndex('purchases', 'purchases_user_id_course_id_status_index')) {
                $table->index(['user_id', 'course_id', 'status'], 'purchases_user_id_course_id_status_index');
            }
            if (!Schema::hasIndex('purchases', 'purchases_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'purchases_user_id_created_at_index');
            }
            if (!Schema::hasIndex('purchases', 'purchases_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'purchases_status_created_at_index');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasIndex('lessons', 'lessons_course_id_status_index')) {
                $table->index(['course_id', 'status'], 'lessons_course_id_status_index');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_is_instructor_index')) {
                $table->index('is_instructor', 'users_is_instructor_index');
            }
            if (!Schema::hasIndex('users', 'users_is_active_created_at_index')) {
                $table->index(['is_active', 'created_at'], 'users_is_active_created_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['status', 'featured']);
            $table->dropIndex(['instructor_id', 'status']);
            $table->dropIndex(['difficulty_level', 'status']);
            $table->dropIndex(['slug']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'course_id']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'course_id', 'status']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'order']);
            $table->dropIndex(['course_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_instructor']);
            $table->dropIndex(['is_active', 'created_at']);
        });
    }
};
