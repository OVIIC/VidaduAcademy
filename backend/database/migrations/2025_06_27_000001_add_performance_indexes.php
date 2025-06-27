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
            if (!$this->indexExists('courses', 'courses_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!$this->indexExists('courses', 'courses_status_featured_index')) {
                $table->index(['status', 'featured']);
            }
            if (!$this->indexExists('courses', 'courses_instructor_id_status_index')) {
                $table->index(['instructor_id', 'status']);
            }
            if (!$this->indexExists('courses', 'courses_difficulty_level_status_index')) {
                $table->index(['difficulty_level', 'status']);
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (!$this->indexExists('enrollments', 'enrollments_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!$this->indexExists('purchases', 'purchases_user_id_course_id_status_index')) {
                $table->index(['user_id', 'course_id', 'status']);
            }
            if (!$this->indexExists('purchases', 'purchases_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
            if (!$this->indexExists('purchases', 'purchases_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (!$this->indexExists('lessons', 'lessons_course_id_status_index')) {
                $table->index(['course_id', 'status']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_is_instructor_index')) {
                $table->index('is_instructor');
            }
            if (!$this->indexExists('users', 'users_is_active_created_at_index')) {
                $table->index(['is_active', 'created_at']);
            }
        });
    }

    private function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        $dbSchemaManager = $connection->getDoctrineSchemaManager();
        $indexes = $dbSchemaManager->listTableIndexes($table);
        return array_key_exists($index, $indexes);
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
