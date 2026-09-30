<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up(): void {
  Schema::create('letter_types', function(Blueprint $t){
   $t->id(); $t->uuid('uuid')->unique(); $t->string('code',30)->unique(); $t->string('name',120);
   $t->string('number_format',150)->nullable(); $t->json('fields_schema')->nullable();
   $t->boolean('is_active')->default(true)->index(); $t->unsignedBigInteger('version')->default(1); $t->timestamps(); $t->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('letter_types'); }
};