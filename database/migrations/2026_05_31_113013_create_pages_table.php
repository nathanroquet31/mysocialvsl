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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();           // ex: karine → vslof.com/karine
            $table->string('model_name');               // "Karine"
            $table->string('model_handle')->nullable(); // "@karinefrenchwoman"
            $table->string('bio')->nullable();
            $table->string('video_url')->nullable();    // Cloudflare Stream URL
            $table->string('bg_color')->default('#0d0d0d');
            $table->string('btn_color')->default('#00aff0');
            $table->boolean('age_gate')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
