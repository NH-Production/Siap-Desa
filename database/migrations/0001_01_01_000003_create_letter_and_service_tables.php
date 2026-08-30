<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('template_view')->default('letters.templates.default');
            $table->json('fields_schema')->nullable();
            $table->string('number_format')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->foreignId('letter_type_id')->constrained('letter_types')->cascadeOnDelete();
            $table->string('letter_number')->nullable()->index();
            $table->string('draft_number')->nullable();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('applicant_nik', 20)->nullable();
            $table->text('applicant_address')->nullable();
            $table->text('purpose')->nullable();
            $table->json('letter_data')->nullable();
            $table->string('status', 20)->default('DRAFT'); // DRAFT, REVIEW, APPROVED, REJECTED, ARCHIVED
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('issued_at')->nullable();
            $table->string('qr_verification_token', 64)->nullable()->unique();
            $table->string('pdf_path')->nullable();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('letter_dispositions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('instructions');
            $table->string('status', 20)->default('PENDING');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->integer('processing_days')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('applicant_nik', 20)->nullable();
            $table->string('applicant_phone', 25)->nullable();
            $table->string('status', 20)->default('SUBMITTED'); // SUBMITTED, VERIFIED, APPROVED, REJECTED, COMPLETED, CANCELLED
            $table->json('request_data')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('services');
        Schema::dropIfExists('letter_dispositions');
        Schema::dropIfExists('letters');
        Schema::dropIfExists('letter_types');
    }
};
