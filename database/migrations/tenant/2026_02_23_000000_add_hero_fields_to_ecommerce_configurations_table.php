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
            $table->string('hero_title')->nullable()->after('banner_path');
            $table->string('hero_subtitle')->nullable()->after('hero_title');
            $table->string('products_section_title')->default('Nuestros Productos')->after('hero_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_configurations', function (Blueprint $table) {
            $table->dropColumn(['hero_title', 'hero_subtitle', 'products_section_title']);
        });
    }
};
