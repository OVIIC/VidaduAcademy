<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('website')->nullable()->after('bio');
            $table->string('youtube_channel')->nullable()->after('website');
            $table->unsignedInteger('subscribers_count')->default(0)->after('youtube_channel');
            $table->boolean('is_instructor')->default(false)->after('subscribers_count');
            $table->boolean('is_active')->default(true)->after('is_instructor');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'bio',
                'website',
                'youtube_channel',
                'subscribers_count',
                'is_instructor',
                'is_active'
            ]);
        });
    }
};
