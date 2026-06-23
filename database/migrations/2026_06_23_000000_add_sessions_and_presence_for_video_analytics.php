<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Real VSL analytics: session-level tracking.
 *
 * - `session_id` groups every event of one visit so 1 visit = 1 view (a person
 *   firing ~10 events counts once; a later re-watch = a new session = a 2nd view).
 * - `type` is relaxed from a rigid enum to a plain string so new event kinds
 *   (video_position, heartbeat, …) no longer get rejected by the DB. The
 *   controller's validation stays the real gatekeeper.
 * - `page_presence` powers live presence (who's on a page right now and since
 *   when) without bloating analytics_events with a heartbeat row every ~12s.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite can't drop the enum CHECK in place — rebuild the table.
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE analytics_events RENAME TO analytics_events_old');

            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained()->onDelete('cascade');
                $table->string('type'); // plain string, validated in the controller
                $table->foreignId('page_link_id')->nullable()->constrained()->onDelete('set null');
                $table->string('country', 2)->nullable();
                $table->string('device')->nullable();
                $table->float('value')->nullable();
                $table->string('referrer', 500)->nullable();
                $table->string('session_id', 40)->nullable()->index();
                $table->timestamp('created_at');
            });

            DB::statement('INSERT INTO analytics_events (id, page_id, type, page_link_id, country, device, value, referrer, created_at) SELECT id, page_id, type, page_link_id, country, device, value, referrer, created_at FROM analytics_events_old');
            DB::statement('DROP TABLE analytics_events_old');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('analytics_events', function (Blueprint $table) {
                $table->string('session_id', 40)->nullable()->after('value')->index();
            });

            if ($driver === 'pgsql') {
                // Laravel maps enum() to a varchar + CHECK constraint on Postgres.
                DB::statement('ALTER TABLE analytics_events DROP CONSTRAINT IF EXISTS analytics_events_type_check');
                DB::statement('ALTER TABLE analytics_events ALTER COLUMN type TYPE varchar(255)');
            } else { // mysql / mariadb
                DB::statement("ALTER TABLE analytics_events MODIFY COLUMN type VARCHAR(255) NOT NULL");
            }
        }

        Schema::create('page_presence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 40);
            $table->string('country', 2)->nullable();
            $table->string('device')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('last_seen_at');
            $table->unique(['page_id', 'session_id']);
            $table->index(['page_id', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_presence');

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('analytics_events', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        } else {
            Schema::table('analytics_events', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        }
    }
};
