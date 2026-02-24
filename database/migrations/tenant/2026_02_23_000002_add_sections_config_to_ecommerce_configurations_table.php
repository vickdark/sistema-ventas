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
            $table->boolean('show_search_bar')->default(true)->after('footer_info');
            $table->boolean('show_categories_section')->default(true)->after('show_search_bar');
            $table->boolean('show_benefits_section')->default(true)->after('show_categories_section');
            
            // Benefits 1
            $table->string('benefit_1_icon')->nullable()->default('fas fa-shipping-fast');
            $table->string('benefit_1_title')->nullable()->default('Envío Rápido');
            $table->string('benefit_1_desc')->nullable()->default('Entregamos tus pedidos en tiempo récord.');
            
            // Benefits 2
            $table->string('benefit_2_icon')->nullable()->default('fas fa-lock');
            $table->string('benefit_2_title')->nullable()->default('Pago Seguro');
            $table->string('benefit_2_desc')->nullable()->default('Tus transacciones están protegidas.');
            
            // Benefits 3
            $table->string('benefit_3_icon')->nullable()->default('fas fa-headset');
            $table->string('benefit_3_title')->nullable()->default('Soporte 24/7');
            $table->string('benefit_3_desc')->nullable()->default('Estamos aquí para ayudarte en todo momento.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'show_search_bar',
                'show_categories_section',
                'show_benefits_section',
                'benefit_1_icon', 'benefit_1_title', 'benefit_1_desc',
                'benefit_2_icon', 'benefit_2_title', 'benefit_2_desc',
                'benefit_3_icon', 'benefit_3_title', 'benefit_3_desc',
            ]);
        });
    }
};
