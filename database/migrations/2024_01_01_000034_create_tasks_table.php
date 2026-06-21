<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('task_type', 24)->default('Action');
            $table->string('status', 24)->default('Open')->index();
            $table->string('priority', 16)->default('Medium');
            $table->foreignUuid('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('resource_type', 32)->nullable()->index();
            $table->string('resource_id', 64)->nullable();
            $table->string('resource_label', 256)->nullable();
            $table->string('decision', 16)->nullable();
            $table->text('decision_comment')->nullable();
            $table->foreignUuid('decided_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
