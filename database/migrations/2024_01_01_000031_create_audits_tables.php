<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('audit_type', 32);
            $table->string('status', 32)->default('Planned');
            $table->foreignUuid('lead_auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('scope')->nullable();
            $table->text('conclusion')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_findings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->foreignUuid('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->foreignUuid('control_id')->nullable()->constrained('controls')->nullOnDelete();
            $table->string('finding_type', 64);
            $table->text('description');
            $table->string('severity', 16)->default('Medium');
            $table->text('corrective_action')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 32)->default('Open');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_findings');
        Schema::dropIfExists('audits');
    }
};
