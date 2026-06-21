<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('name', 256);
            $table->text('description')->nullable();
            $table->string('category', 32)->default('Service');
            $table->text('service_description')->nullable();
            $table->string('criticality', 16)->default('Medium');
            $table->string('data_classification', 32)->default('Internal');
            $table->string('status', 32)->default('Active');
            $table->boolean('is_requirements_agreed')->default(false);
            $table->boolean('right_to_audit')->default(false);
            $table->boolean('processes_pii')->default(false);
            $table->string('certifications', 256)->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
