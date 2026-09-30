<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up():void{Schema::create('finance_budgets',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->uuid('village_id')->index();$t->foreignId('account_id')->constrained('finance_accounts')->cascadeOnDelete();$t->unsignedSmallInteger('fiscal_year');$t->decimal('budgeted_amount',18,2)->default(0);$t->unsignedBigInteger('version')->default(1);$t->timestamps();$t->unique(['account_id','fiscal_year']);
 });}
 public function down():void{Schema::dropIfExists('finance_budgets');}
};