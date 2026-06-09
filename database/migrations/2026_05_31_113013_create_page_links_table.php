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
        Schema::create('page_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['onlyfans', 'mym', 'telegram', 'instagram', 'custom']);
            $table->string('label');                    // "Mon OnlyFans — Accès privé"
            $table->string('url');
            $table->string('btn_color')->nullable();    // override couleur si besoin
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_links');
    }
};
