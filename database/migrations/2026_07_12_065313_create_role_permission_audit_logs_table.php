<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("role_permission_audit_logs", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("actor_id")->nullable();
            $table->foreign("actor_id")->references("id")->on("users")->nullOnDelete();
            $table->foreignId("target_user_id")->constrained("users")->cascadeOnDelete();
            $table->string("action");
            $table->string("subject_type");
            $table->unsignedBigInteger("subject_id")->nullable();
            $table->string("subject_name")->nullable();
            $table->ipAddress("ip_address")->nullable();
            $table->text("user_agent")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("role_permission_audit_logs");
    }
};
