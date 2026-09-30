<?php
use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;
return new class extends Migration {
 public function up():void{Schema::create('finance_transactions',function(Blueprint $t){
  $t->id();$t->uuid('uuid')->unique();$t->uuid('village_id')->index();$t->foreignId('account_id')->constrained('finance_accounts')->restrictOnDelete();
  $t->string('transaction_number',80)->unique();$t->date('transaction_date')->index();$t->enum('type',['PENERIMAAN','PENGELUARAN','MUTASI']);$t->decimal('amount',18,2);$t->text('description');
  $t->string('recipient_or_payer',150)->nullable();$t->enum('payment_method',['TUNAI','TRANSFER']);$t->string('spj_number',80)->nullable()->index();
  $t->enum('status',['DRAFT','POSTED','VOID'])->default('DRAFT')->index();$t->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamp('posted_at')->nullable();
  $t->unsignedBigInteger('version')->default(1);$t->timestamps();$t->softDeletes();
 });}
 public function down():void{Schema::dropIfExists('finance_transactions');}
};