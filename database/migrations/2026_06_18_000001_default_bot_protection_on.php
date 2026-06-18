<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shield Protection™ must be opt-out, not opt-in. The original column defaulted
 * to false, so any page created without explicitly toggling it shipped with no
 * server-side cloaking — exposing the real page (and avatar as og:image) to
 * Meta's crawler. Flip the default on, and protect pages that were left
 * unprotected by the old default.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('bot_protection')->default(true)->change();
        });

        // Backfill: the old default left these unprotected unintentionally.
        DB::table('pages')->where('bot_protection', false)->update(['bot_protection' => true]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('bot_protection')->default(false)->change();
        });
    }
};
