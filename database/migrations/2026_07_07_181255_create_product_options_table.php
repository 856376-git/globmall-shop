<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('product_id');
        });
    }
    public function down(): void { Schema::dropIfExists('product_options'); }
};