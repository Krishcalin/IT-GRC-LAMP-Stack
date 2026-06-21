<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('file_name', 512);
            $table->string('file_path', 1024);
            $table->string('file_type', 128)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('control_id')->nullable()->constrained('controls')->nullOnDelete();
            $table->foreignUuid('risk_id')->nullable()->constrained('risks')->nullOnDelete();
            $table->foreignUuid('audit_id')->nullable()->constrained('audits')->nullOnDelete();
            $table->foreignUuid('policy_id')->nullable()->constrained('policies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
