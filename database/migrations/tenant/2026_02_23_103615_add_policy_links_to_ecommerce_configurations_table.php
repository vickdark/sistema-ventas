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
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->string('shipping_policy_link')->nullable();
            $table->string('returns_policy_link')->nullable();
            $table->string('terms_conditions_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->dropColumn(['shipping_policy_link', 'returns_policy_link', 'terms_conditions_link']);
        });
    }
};
