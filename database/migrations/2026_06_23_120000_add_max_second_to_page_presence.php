<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Store the furthest second each visit reached, in place on its presence row.
 * One row per session, updated continuously — gives a precise per-second
 * retention curve without appending a video_position event every second.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_presence', function (Blueprint $table) {
            $table->unsignedInteger('max_second')->default(0)->after('device');
        });
    }

    public function down(): void
    {
        Schema::table('page_presence', function (Blueprint $table) {
            $table->dropColumn('max_second');
        });
    }
};
