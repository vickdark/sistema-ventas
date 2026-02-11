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
        Schema::connection('central')->create('http_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // null if central
            $table->string('method');
            $table->string('url');
            $table->integer('status');
            $table->decimal('duration', 8, 4)->nullable(); // in seconds
            $table->string('ip')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('http_logs');
    }
};
