<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar', 500)->nullable()->after('email');
            $table->string('phone', 30)->nullable()->after('avatar');
            $table->enum('role', ['customer', 'admin', 'seller'])->default('customer')->after('phone');
            $table->tinyInteger('status')->default(1)->after('role');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'phone', 'role', 'status']);
        });
    }
};