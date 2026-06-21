<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('clause', 16)->unique();
            $table->string('title', 256);
            $table->text('description');
            $table->string('framework', 48)->default('ISO 27001:2022')->index();
            $table->string('theme', 32);
            $table->text('implementation_guidance')->nullable();
            $table->string('status', 32)->default('Not Started');
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });

        Schema::create('control_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('source_control_id')->constrained('controls')->cascadeOnDelete();
            $table->foreignUuid('target_control_id')->constrained('controls')->cascadeOnDelete();
            $table->string('relationship_type', 16)->default('related');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['source_control_id', 'target_control_id'], 'uq_control_mapping');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_mappings');
        Schema::dropIfExists('controls');
    }
};
