<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Per-page white-label toggle: show the "Powered by MySocialVSL" footer
            // on this page. Agency-only perk (enforced server-side); for every other
            // plan the footer is always shown regardless of this column. Default
            // false = white-label, preserving today's behaviour for existing Agency
            // pages — see ResolvesPublicPages + PageController.
            $table->boolean('show_branding')->default(false)->after('link_preview_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('show_branding');
        });
    }
};
