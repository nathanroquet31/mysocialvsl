<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // VSL placement in landing layout (existing video settings untouched)
            $table->boolean('vsl_enabled')->default(true)->after('video_url');
            $table->string('vsl_position', 10)->default('top')->after('vsl_enabled'); // top | middle | bottom

            // FOMO modal
            $table->boolean('fomo_enabled')->default(false)->after('promo_text');
            $table->string('fomo_title')->nullable()->after('fomo_enabled');
            $table->string('fomo_message', 500)->nullable()->after('fomo_title');
            $table->string('fomo_cta_label', 100)->nullable()->after('fomo_message');
            $table->unsignedInteger('fomo_delay_seconds')->default(5)->after('fomo_cta_label');

            // SEO / Open Graph
            $table->string('meta_title', 120)->nullable()->after('fomo_delay_seconds');
            $table->string('meta_description', 300)->nullable()->after('meta_title');
            $table->string('og_image_url')->nullable()->after('meta_description');

            // UTM forwarding to destination links
            $table->boolean('utm_passthrough')->default(true)->after('og_image_url');
        });

        Schema::table('page_links', function (Blueprint $table) {
            $table->string('title')->nullable()->after('label');      // image buttons / creator cards
            $table->string('subtitle')->nullable()->after('title');
            $table->json('meta')->nullable()->after('image_url');     // carousel/grid images, countdown config, price…
            $table->boolean('is_visible')->default(true)->after('order');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone', 64)->default('UTC')->after('email');
            $table->json('preferences')->nullable()->after('timezone'); // notifications, language…
            $table->string('avatar_url')->nullable()->after('preferences');
            $table->string('affiliate_code', 32)->nullable()->unique()->after('avatar_url');
            $table->foreignId('referred_by')->nullable()->after('affiliate_code')->constrained('users')->nullOnDelete();
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('kind', 20)->default('session')->after('name'); // session | api
            $table->string('ip_address', 45)->nullable()->after('kind');
            $table->string('user_agent', 500)->nullable()->after('ip_address');
        });

        // Architecture for Social Analytics (Instagram connection via official API later)
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform', 30)->default('instagram');
            $table->string('username');
            $table->string('external_id')->nullable();
            $table->text('access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('tags')->nullable();
            $table->json('stats')->nullable(); // followers, views, posts, last_reel_views…
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'platform', 'username']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['kind', 'ip_address', 'user_agent']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn(['timezone', 'preferences', 'avatar_url', 'affiliate_code']);
        });
        Schema::table('page_links', function (Blueprint $table) {
            $table->dropColumn(['title', 'subtitle', 'meta', 'is_visible']);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'vsl_enabled', 'vsl_position',
                'fomo_enabled', 'fomo_title', 'fomo_message', 'fomo_cta_label', 'fomo_delay_seconds',
                'meta_title', 'meta_description', 'og_image_url', 'utm_passthrough',
            ]);
        });
    }
};
