<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Profile
            $table->string('template')->default('classic')->after('slug');
            $table->string('avatar_url')->nullable()->after('bio');
            $table->string('bg_image_url')->nullable()->after('avatar_url');
            $table->boolean('verified_badge')->default(false)->after('bg_image_url');
            $table->boolean('show_avatar')->default(true)->after('verified_badge');

            // Advanced
            $table->boolean('online_status')->default(false)->after('age_gate');
            $table->string('location')->nullable()->after('online_status');
            $table->string('response_time')->nullable()->after('location');
            $table->timestamp('countdown_end')->nullable()->after('response_time');
            $table->string('promo_text')->nullable()->after('countdown_end');

            // Settings
            $table->boolean('bot_protection')->default(false)->after('promo_text');
            $table->boolean('deep_link_enabled')->default(true)->after('bot_protection');
            $table->boolean('strict_deep_link')->default(false)->after('deep_link_enabled');
            $table->boolean('link_preview_enabled')->default(true)->after('strict_deep_link');
            $table->string('custom_domain')->nullable()->after('link_preview_enabled');
        });

        // Extend page_links type to string (supports more types)
        Schema::table('page_links', function (Blueprint $table) {
            $table->string('type', 50)->change();
            $table->string('icon')->nullable()->after('type');
            $table->string('image_url')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'template', 'avatar_url', 'bg_image_url', 'verified_badge', 'show_avatar',
                'online_status', 'location', 'response_time', 'countdown_end', 'promo_text',
                'bot_protection', 'deep_link_enabled', 'strict_deep_link', 'link_preview_enabled', 'custom_domain',
            ]);
        });

        Schema::table('page_links', function (Blueprint $table) {
            $table->dropColumn(['icon', 'image_url']);
        });
    }
};
