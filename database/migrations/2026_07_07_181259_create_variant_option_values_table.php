<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('variant_option_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('option_value_id');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('option_value_id')->references('id')->on('product_option_values')->onDelete('cascade');
            $table->unique(['variant_id', 'option_value_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('variant_option_values'); }
};