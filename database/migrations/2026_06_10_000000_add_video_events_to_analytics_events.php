<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('ALTER TABLE analytics_events RENAME TO analytics_events_old');

            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained()->onDelete('cascade');
                $table->enum('type', ['page_view', 'link_click', 'age_confirmed', 'video_play', 'video_progress', 'video_unmute']);
                $table->foreignId('page_link_id')->nullable()->constrained()->onDelete('set null');
                $table->string('country', 2)->nullable();
                $table->string('device')->nullable();
                $table->float('value')->nullable();
                $table->timestamp('created_at');
            });

            DB::statement('INSERT INTO analytics_events (id, page_id, type, page_link_id, country, device, created_at) SELECT id, page_id, type, page_link_id, country, device, created_at FROM analytics_events_old');
            DB::statement('DROP TABLE analytics_events_old');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('analytics_events', function (Blueprint $table) {
                $table->float('value')->nullable()->after('device');
            });
            DB::statement("ALTER TABLE analytics_events MODIFY COLUMN type ENUM('page_view','link_click','age_confirmed','video_play','video_progress','video_unmute')");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['page_view', 'link_click', 'age_confirmed']);
            $table->foreignId('page_link_id')->nullable()->constrained()->onDelete('set null');
            $table->string('country', 2)->nullable();
            $table->string('device')->nullable();
            $table->timestamp('created_at');
        });
    }
};
