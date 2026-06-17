<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedSmallInteger('popup_delay_seconds')->default(5)->after('cta_reveal_at');
            $table->string('popup_image_url')->nullable()->after('popup_delay_seconds');
            $table->text('popup_text')->nullable()->after('popup_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['popup_delay_seconds', 'popup_image_url', 'popup_text']);
        });
    }
};
