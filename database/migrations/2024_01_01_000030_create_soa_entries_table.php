<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soa_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('control_id')->unique()->constrained('controls')->cascadeOnDelete();
            $table->boolean('applicable')->default(true);
            $table->text('justification')->nullable();
            $table->string('implementation_status', 32)->default('Not Implemented');
            $table->text('implementation_evidence')->nullable();
            $table->foreignUuid('responsible_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soa_entries');
    }
};
