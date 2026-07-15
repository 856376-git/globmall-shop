<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained()->onDelete('cascade');
            $table->string('value', 100);
            $table->string('sku_suffix', 50)->nullable();
            $table->decimal('price_offset', 10, 2)->default(0);
            $table->integer('stock')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('product_option_id');
        });
    }
    public function down(): void { Schema::dropIfExists('product_option_values'); }
};