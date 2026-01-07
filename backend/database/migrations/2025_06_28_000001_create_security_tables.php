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
        Schema::dropIfExists('security_logs');
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50)->index(); // login, logout, purchase, enrollment, etc.
            $table->string('severity', 20)->default('info'); // info, warning, error, critical
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->index(); // IPv6 support
            $table->string('user_agent', 500)->nullable();
            $table->string('action', 100)->default('unknown'); // specific action performed
            $table->json('metadata')->nullable(); // additional context data
            $table->string('resource_type', 50)->nullable(); // course, user, payment, etc.
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->boolean('is_suspicious')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->index();
            
            // Indexes for common queries
            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_suspicious', 'created_at']);
            $table->index(['severity', 'created_at']);
        });

        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('ip_address', 45)->index();
            $table->string('user_agent', 500)->nullable();
            $table->integer('attempts')->default(1);
            $table->timestamp('last_attempt')->index();
            $table->timestamp('locked_until')->nullable()->index();
            $table->timestamps();
            
            $table->unique(['email', 'ip_address']);
        });

        Schema::create('suspicious_activities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // xss_attempt, sql_injection, rate_limit_exceeded, etc.
            $table->string('ip_address', 45)->index();
            $table->string('user_agent', 500)->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('payload')->nullable(); // The suspicious input
            $table->string('endpoint', 255)->nullable(); // Which endpoint was targeted
            $table->string('method', 10)->nullable(); // HTTP method
            $table->json('headers')->nullable(); // Request headers
            $table->boolean('blocked')->default(false); // Was the request blocked?
            $table->integer('risk_score')->default(0)->index(); // Risk assessment score
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'created_at']);
            $table->index(['risk_score', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspicious_activities');
        Schema::dropIfExists('failed_login_attempts');
        Schema::dropIfExists('security_logs');
    }
};
