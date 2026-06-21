<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref_id', 32)->unique();
            $table->string('name', 256);
            $table->text('description')->nullable();
            $table->string('asset_type', 32);
            $table->string('classification', 32)->default('Internal');
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department', 128)->nullable();
            $table->string('location', 256)->nullable();
            $table->string('status', 32)->default('Active');
            $table->string('criticality', 16)->default('Medium');
            $table->timestamps();
        });

        Schema::create('asset_risks', function (Blueprint $table) {
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('risk_id')->constrained('risks')->cascadeOnDelete();
            $table->primary(['asset_id', 'risk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_risks');
        Schema::dropIfExists('assets');
    }
};
