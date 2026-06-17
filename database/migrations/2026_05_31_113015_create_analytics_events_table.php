<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['page_view', 'link_click', 'age_confirmed']);
            $table->foreignId('page_link_id')->nullable()->constrained()->onDelete('set null');
            $table->string('country', 2)->nullable();  // "FR", "US"
            $table->string('device')->nullable();       // "mobile", "desktop"
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
