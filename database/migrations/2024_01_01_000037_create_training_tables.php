<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('training_type', 48)->default('Awareness Campaign');
            $table->string('topic', 128)->nullable();
            $table->string('clause_ref', 16)->default('7.3');
            $table->string('status', 32)->default('Planned');
            $table->string('audience', 256)->nullable();
            $table->string('materials_link', 512)->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('training_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->foreignUuid('campaign_id')->constrained('training_campaigns')->cascadeOnDelete();
            $table->string('participant', 128);
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 16)->default('Assigned');
            $table->double('score')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('evidence', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
        Schema::dropIfExists('training_campaigns');
    }
};
