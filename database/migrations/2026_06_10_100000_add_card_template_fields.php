<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new display fields to pages
        Schema::table('pages', function (Blueprint $table) {
            $table->string('video_fit', 10)->default('contain')->after('video_url');
            $table->float('overlay_opacity')->default(0.6)->after('video_fit');
            $table->string('card_position', 10)->default('bottom')->after('overlay_opacity');
        });

        // Expand analytics_events enum to add bot_blocked + time_on_page + referrer column
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE analytics_events RENAME TO analytics_events_old');

            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained()->onDelete('cascade');
                $table->enum('type', [
                    'page_view', 'link_click', 'age_confirmed',
                    'video_play', 'video_progress', 'video_unmute',
                    'bot_blocked', 'time_on_page',
                ]);
                $table->foreignId('page_link_id')->nullable()->constrained()->onDelete('set null');
                $table->string('country', 2)->nullable();
                $table->string('device')->nullable();
                $table->float('value')->nullable();
                $table->string('referrer', 500)->nullable();
                $table->timestamp('created_at');
            });

            DB::statement('INSERT INTO analytics_events (id, page_id, type, page_link_id, country, device, value, created_at) SELECT id, page_id, type, page_link_id, country, device, value, created_at FROM analytics_events_old');
            DB::statement('DROP TABLE analytics_events_old');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('analytics_events', function (Blueprint $table) {
                $table->string('referrer', 500)->nullable()->after('value');
            });
            DB::statement("ALTER TABLE analytics_events MODIFY COLUMN type ENUM('page_view','link_click','age_confirmed','video_play','video_progress','video_unmute','bot_blocked','time_on_page')");
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['video_fit', 'overlay_opacity', 'card_position']);
        });
    }
};
