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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('service_type')->default('subscription')->after('id'); // subscription, purchase
            $table->integer('subscription_period')->nullable()->after('service_type'); // 30, 90, 365
            $table->date('next_payment_date')->nullable()->after('subscription_period');
            $table->boolean('is_paid')->default(true)->after('next_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'subscription_period', 'next_payment_date', 'is_paid']);
        });
    }
};
