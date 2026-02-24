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
            $table->boolean('top_bar_active')->default(true)->after('show_benefits_section');
            $table->string('top_bar_text')->nullable()->default('¡Oferta Especial! 20% de descuento en tu primera compra')->after('top_bar_active');
            $table->string('top_bar_link')->nullable()->after('top_bar_text');
            $table->string('top_bar_bg_color')->default('#000000')->after('top_bar_link');
            $table->string('top_bar_text_color')->default('#ffffff')->after('top_bar_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'top_bar_active',
                'top_bar_text',
                'top_bar_link',
                'top_bar_bg_color',
                'top_bar_text_color',
            ]);
        });
    }
};
