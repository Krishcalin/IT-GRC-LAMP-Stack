<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('category', 48)->default('Other');
            $table->string('severity', 16)->default('Medium');
            $table->string('status', 32)->default('New');
            $table->string('reporter', 128)->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('risk_id')->nullable()->constrained('risks')->nullOnDelete();
            $table->text('affected_assets')->nullable();
            $table->boolean('data_breach')->default(false);
            $table->text('containment_actions')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->text('evidence_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
