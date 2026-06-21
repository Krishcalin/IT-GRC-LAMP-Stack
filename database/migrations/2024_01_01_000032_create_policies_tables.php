<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('version', 16)->default('1.0');
            $table->string('status', 32)->default('Draft');
            $table->string('category', 64);
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Schema::create('policy_acknowledgments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique(['policy_id', 'user_id'], 'uq_policy_ack');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_acknowledgments');
        Schema::dropIfExists('policies');
    }
};
