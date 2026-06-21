<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('name', 256);
            $table->text('description')->nullable();
            $table->string('metric_type', 8)->default('KPI');
            $table->string('clause_ref', 16)->default('9.1');
            $table->foreignUuid('objective_id')->nullable()->constrained('objectives')->nullOnDelete();
            $table->double('target_value')->nullable();
            $table->double('current_value')->nullable();
            $table->string('unit', 32)->nullable();
            $table->string('direction', 20)->default('higher_is_better');
            $table->string('frequency', 32)->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('last_measured')->nullable();
            $table->timestamps();
        });

        Schema::create('metric_measurements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('metric_id')->constrained('metrics')->cascadeOnDelete();
            $table->double('value');
            $table->text('note')->nullable();
            $table->date('captured_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_measurements');
        Schema::dropIfExists('metrics');
    }
};
