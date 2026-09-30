<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up(): void {
  Schema::create('service_requests', function(Blueprint $t){
   $t->id(); $t->uuid('uuid')->unique(); $t->uuid('village_id')->index(); $t->foreignId('service_id')->constrained('services')->restrictOnDelete(); $t->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
   $t->string('applicant_name',150); $t->string('applicant_nik',16)->nullable()->index(); $t->string('applicant_phone',25)->nullable(); $t->text('notes')->nullable(); $t->json('request_data')->nullable();
   $t->enum('status',['SUBMITTED','VERIFIED','PROCESSING','COMPLETED','REJECTED','CANCELLED'])->default('SUBMITTED')->index(); $t->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamp('processed_at')->nullable(); $t->unsignedBigInteger('version')->default(1); $t->timestamps(); $t->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('service_requests'); }
};