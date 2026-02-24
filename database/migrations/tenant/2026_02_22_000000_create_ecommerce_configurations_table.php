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
        Schema::create('ecommerce_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('primary_color')->default('#3b82f6'); // Azul por defecto
            $table->string('secondary_color')->default('#1e3a8a'); // Azul oscuro por defecto
            $table->text('about_us_text')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_configurations');
    }
};