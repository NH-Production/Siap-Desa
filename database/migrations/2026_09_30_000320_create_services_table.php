<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up(): void {
  Schema::create('services', function(Blueprint $t){
   $t->id(); $t->uuid('uuid')->unique(); $t->string('code',30)->unique(); $t->string('name',120); $t->text('description')->nullable(); $t->json('requirements')->nullable();
   $t->boolean('is_active')->default(true)->index(); $t->unsignedBigInteger('version')->default(1); $t->timestamps(); $t->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('services'); }
};