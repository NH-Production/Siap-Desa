<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up():void{Schema::create('finance_transaction_items',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->foreignId('transaction_id')->constrained('finance_transactions')->cascadeOnDelete();$t->string('description',255);$t->decimal('amount',18,2);$t->unsignedBigInteger('version')->default(1);$t->timestamps();
 });}
 public function down():void{Schema::dropIfExists('finance_transaction_items');}
};