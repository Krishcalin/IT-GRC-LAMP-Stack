<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documented_information', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('doc_type', 32);
            $table->string('clause_ref', 32)->nullable()->index();
            $table->boolean('mandatory')->default(false);
            $table->string('version', 16)->default('0.1');
            $table->string('status', 32)->default('Draft');
            $table->string('classification', 32)->default('Internal');
            $table->string('location', 512)->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('review_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documented_information');
    }
};
