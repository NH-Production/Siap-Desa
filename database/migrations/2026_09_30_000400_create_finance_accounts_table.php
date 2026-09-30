<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up(): void { Schema::create('finance_accounts',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->uuid('village_id')->index();$t->foreignId('parent_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
  $t->string('code',30)->unique();$t->string('name',150);$t->enum('type',['PENDAPATAN','BELANJA','PEMBIAYAAN','KAS']);$t->boolean('is_active')->default(true);$t->unsignedBigInteger('version')->default(1);$t->timestamps();$t->softDeletes();
 });}
 public function down():void{Schema::dropIfExists('finance_accounts');}
};