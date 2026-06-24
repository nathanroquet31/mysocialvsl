<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A hand-picked affiliate (founder-only flag, like is_beta_partner). Only
        // affiliates earn commissions and get an affiliate dashboard/link; regular
        // signups never self-enrol. Distinct from is_beta_partner (free trial, no $).
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_affiliate')->default(false)->after('is_beta_partner');
        });

        // One row per paid invoice of a referred customer = 20% recurring commission
        // owed to the referring affiliate. stripe_invoice_id is unique so webhook
        // retries / duplicate deliveries can never double-credit (idempotency).
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('stripe_invoice_id')->unique();
            $table->unsignedBigInteger('amount_paid_cents'); // what the customer actually paid
            $table->unsignedBigInteger('commission_cents');  // 20% of amount_paid_cents
            $table->string('currency', 3)->default('usd');
            $table->decimal('rate', 5, 4)->default(0.2000);  // snapshot of the rate applied
            $table->string('status')->default('pending');     // pending → paid (manual payout)
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_affiliate');
        });
    }
};
