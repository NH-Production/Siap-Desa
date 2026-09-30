<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up():void{Schema::create('spj',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->uuid('village_id')->index();$t->string('spj_number',80)->unique();$t->foreignId('transaction_id')->unique()->constrained('finance_transactions')->cascadeOnDelete();
  $t->date('date');$t->string('activity_name',200);$t->text('description')->nullable();$t->enum('status',['DRAFT','SUBMITTED','VERIFIED','REJECTED'])->default('DRAFT')->index();
  $t->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamp('verified_at')->nullable();$t->unsignedBigInteger('version')->default(1);$t->timestamps();$t->softDeletes();
 });}
 public function down():void{Schema::dropIfExists('spj');}
};