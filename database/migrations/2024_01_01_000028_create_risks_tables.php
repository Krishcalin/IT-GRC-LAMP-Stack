<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description');
            $table->string('category', 64);
            $table->integer('likelihood')->default(1);
            $table->integer('impact')->default(1);
            $table->string('inherent_risk_level', 16)->default('Low');
            $table->string('treatment', 32)->default('Mitigate');
            $table->text('treatment_plan')->nullable();
            $table->integer('residual_likelihood')->nullable();
            $table->integer('residual_impact')->nullable();
            $table->string('residual_risk_level', 16)->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('Open');
            $table->date('review_date')->nullable();
            $table->timestamps();
        });

        Schema::create('risk_controls', function (Blueprint $table) {
            $table->foreignUuid('risk_id')->constrained('risks')->cascadeOnDelete();
            $table->foreignUuid('control_id')->constrained('controls')->cascadeOnDelete();
            $table->primary(['risk_id', 'control_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_controls');
        Schema::dropIfExists('risks');
    }
};
