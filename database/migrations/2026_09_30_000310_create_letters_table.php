<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up(): void {
  Schema::create('letters', function(Blueprint $t){
   $t->id(); $t->uuid('uuid')->unique(); $t->uuid('village_id')->index();
   $t->foreignId('letter_type_id')->constrained('letter_types')->restrictOnDelete();
   $t->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
   $t->string('draft_number',60)->unique(); $t->string('letter_number',100)->nullable()->unique();
   $t->string('applicant_name',150); $t->string('applicant_nik',16)->nullable()->index(); $t->text('applicant_address')->nullable(); $t->text('purpose')->nullable();
   $t->json('letter_data')->nullable(); $t->enum('status',['DRAFT','SUBMITTED','APPROVED','REJECTED','CANCELLED'])->default('DRAFT')->index();
   $t->string('qr_verification_token',100)->unique(); $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
   $t->date('issued_at')->nullable(); $t->timestamp('approved_at')->nullable(); $t->unsignedBigInteger('version')->default(1); $t->timestamps(); $t->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('letters'); }
};