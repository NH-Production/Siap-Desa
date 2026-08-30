<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('name');
            $table->integer('year');
            $table->text('description')->nullable();
            $table->decimal('budget_per_recipient', 15, 2)->default(0);
            $table->integer('quota')->default(0);
            $table->string('source', 50)->default('DANA_DESA');
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('aid_recipients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('aid_program_id')->constrained('aid_programs')->cascadeOnDelete();
            $table->foreignId('citizen_id')->constrained('citizens')->cascadeOnDelete();
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->date('distribution_date')->nullable();
            $table->string('status', 25)->default('TERVERIFIKASI'); // TERVERIFIKASI, DISALURKAN, DIBATALKAN
            $table->string('proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('title');
            $table->string('category', 50)->default('UMUM');
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 80)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('aid_recipients');
        Schema::dropIfExists('aid_programs');
    }
};
