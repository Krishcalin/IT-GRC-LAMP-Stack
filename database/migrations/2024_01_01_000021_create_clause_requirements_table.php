<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clause_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('clause', 16)->unique();
            $table->string('title', 256);
            $table->string('section', 64);
            $table->integer('clause_number')->index();
            $table->text('requirement');
            $table->text('documented_info')->nullable();
            $table->string('conformity_status', 32)->default('Not Assessed');
            $table->text('implementation_notes')->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clause_requirements');
    }
};
