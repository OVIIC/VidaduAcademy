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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('users', 'subscriber_count')) {
                $table->string('subscriber_count')->nullable()->after('subscribers_count');
            }
            if (!Schema::hasColumn('users', 'content_niche')) {
                $table->string('content_niche')->nullable()->after('subscriber_count');
            }
            if (!Schema::hasColumn('users', 'goals')) {
                $table->string('goals')->nullable()->after('content_niche');
            }
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('goals');
            }
            if (!Schema::hasColumn('users', 'course_reminders')) {
                $table->boolean('course_reminders')->default(true)->after('email_notifications');
            }
            if (!Schema::hasColumn('users', 'marketing_emails')) {
                $table->boolean('marketing_emails')->default(false)->after('course_reminders');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscriber_count',
                'content_niche',
                'goals',
                'email_notifications',
                'course_reminders',
                'marketing_emails'
            ]);
        });
    }
};
