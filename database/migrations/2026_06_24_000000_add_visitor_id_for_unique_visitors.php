<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unique-visitor tracking, the way Linktree/Linko do it.
 *
 * `session_id` already groups one VISIT (generated per page load, lost on reload),
 * so it gives "total views" (each visit counted, repeat visits counted again).
 * To count a real PERSON once across visits we need a stable id that survives
 * reloads/return visits — that's `visitor_id`, a localStorage UUID set once on the
 * public page. Distinct visitor_id = unique visitors; distinct (visitor_id, click)
 * = unique clicks. Nullable for legacy rows (counts degrade to session level).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            $table->string('visitor_id', 40)->nullable()->after('session_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('analytics_events', function (Blueprint $table) {
            $table->dropColumn('visitor_id');
        });
    }
};
