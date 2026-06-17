<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Chosen Agency capacity. NULL = unlimited (∞ tier). Only meaningful when plan = 'agency'.
            $table->unsignedInteger('agency_pages')->nullable()->after('extra_pages');
            $table->unsignedInteger('agency_links')->nullable()->after('extra_links');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agency_pages', 'agency_links']);
        });
    }
};
