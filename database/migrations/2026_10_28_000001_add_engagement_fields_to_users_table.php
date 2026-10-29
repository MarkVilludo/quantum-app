<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('quantum_xp')->default(0)->after('year_id');
            $table->unsignedInteger('daily_streak')->default(0)->after('quantum_xp');
            $table->date('last_activity_date')->nullable()->after('daily_streak');
            $table->date('lessons_today_date')->nullable()->after('last_activity_date');
            $table->unsignedTinyInteger('lessons_today_count')->default(0)->after('lessons_today_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'quantum_xp',
                'daily_streak',
                'last_activity_date',
                'lessons_today_date',
                'lessons_today_count',
            ]);
        });
    }
};
