<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posture_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('snapshot_date')->unique();
            $table->double('compliance_score')->default(0);
            $table->double('isms_conformity_score')->default(0);
            $table->double('document_readiness_score')->default(0);
            $table->double('training_completion_rate')->default(0);
            $table->integer('implemented_controls')->default(0);
            $table->integer('total_controls')->default(0);
            $table->integer('open_risks')->default(0);
            $table->integer('critical_risks')->default(0);
            $table->integer('open_findings')->default(0);
            $table->integer('open_tasks')->default(0);
            $table->integer('overdue_tasks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posture_snapshots');
    }
};
