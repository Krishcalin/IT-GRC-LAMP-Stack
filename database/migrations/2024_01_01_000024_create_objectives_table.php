<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objectives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->string('clause_ref', 16)->default('6.2');
            $table->text('measure')->nullable();
            $table->string('target_value', 128)->nullable();
            $table->string('current_value', 128)->nullable();
            $table->string('unit', 32)->nullable();
            $table->string('status', 32)->default('Not Started');
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
