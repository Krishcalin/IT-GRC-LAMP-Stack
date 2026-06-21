<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('assessment_type', 40)->default('Control Self-Assessment');
            $table->string('framework', 48)->nullable();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 24)->default('Draft');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->foreignUuid('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignUuid('control_id')->nullable()->constrained('controls')->nullOnDelete();
            $table->text('question')->nullable();
            $table->text('response')->nullable();
            $table->integer('maturity')->nullable();
            $table->string('result', 20)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_items');
        Schema::dropIfExists('assessments');
    }
};
