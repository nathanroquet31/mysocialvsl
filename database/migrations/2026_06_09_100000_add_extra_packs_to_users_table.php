<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('extra_pages')->default(0)->after('plan_expires_at');
            $table->unsignedInteger('extra_links')->default(0)->after('extra_pages');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['extra_pages', 'extra_links']);
        });
    }
};
