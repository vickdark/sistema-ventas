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
            // Featured Section (Split)
            $table->boolean('show_featured_section')->default(true);
            $table->string('featured_title')->nullable()->default('Calidad Premium');
            $table->text('featured_description')->nullable();
            $table->string('featured_btn_text')->nullable()->default('Comprar Ahora');
            $table->string('featured_btn_link')->nullable()->default('/shop/products');
            
            // Testimonials
            $table->boolean('show_testimonials')->default(true);
            $table->string('testimonials_title')->nullable()->default('Lo que dicen nuestros clientes');
            
            // Social Media Extras
            $table->string('tiktok_url')->nullable();
            $table->string('twitter_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'show_featured_section',
                'featured_title',
                'featured_description',
                'featured_btn_text',
                'featured_btn_link',
                'show_testimonials',
                'testimonials_title',
                'tiktok_url',
                'twitter_url',
            ]);
        });
    }
};
