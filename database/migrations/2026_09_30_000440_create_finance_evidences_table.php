<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up():void{Schema::create('finance_evidences',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->foreignId('transaction_id')->constrained('finance_transactions')->cascadeOnDelete();$t->string('file_path',500);$t->string('original_name',255);$t->string('mime_type',120)->nullable();$t->unsignedBigInteger('file_size')->nullable();$t->string('sha256',64)->nullable()->index();$t->unsignedBigInteger('version')->default(1);$t->timestamps();
 });}
 public function down():void{Schema::dropIfExists('finance_evidences');}
};