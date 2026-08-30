<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id')->nullable();
            $table->string('code', 30)->index();
            $table->string('name');
            $table->string('type', 25); // PENDAPATAN, BELANJA, PEMBIAYAAN, KAS
            $table->foreignId('parent_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->integer('fiscal_year')->index();
            $table->foreignId('account_id')->constrained('finance_accounts')->cascadeOnDelete();
            $table->decimal('budgeted_amount', 15, 2)->default(0);
            $table->decimal('revised_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('transaction_number', 50)->index();
            $table->date('transaction_date')->index();
            $table->foreignId('account_id')->constrained('finance_accounts')->cascadeOnDelete();
            $table->string('type', 25); // PENERIMAAN, PENGELUARAN, MUTASI
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('recipient_or_payer')->nullable();
            $table->string('payment_method', 25)->default('TUNAI');
            $table->string('spj_number', 50)->nullable();
            $table->string('status', 20)->default('DRAFT'); // DRAFT, APPROVED, POSTED
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('finance_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('transaction_id')->constrained('finance_transactions')->cascadeOnDelete();
            $table->string('item_name');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_evidence', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('transaction_id')->constrained('finance_transactions')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type', 50)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('asset_code', 50)->index();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->date('acquisition_date')->nullable();
            $table->string('acquisition_source', 80)->nullable();
            $table->decimal('acquisition_cost', 15, 2)->default(0);
            $table->string('condition', 25)->default('BAIK'); // BAIK, RUSAK_RINGAN, RUSAK_BERAT
            $table->string('location')->nullable();
            $table->string('custodian')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('AKTIF'); // AKTIF, MUTASI, DIHAPUSKAN

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_mutations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('mutation_type', 30); // PINDAH_LOKASI, PERUBAHAN_KONDISI, PENGHAPUSAN
            $table->string('previous_value')->nullable();
            $table->string('new_value')->nullable();
            $table->date('date');
            $table->text('reason');
            $table->string('authorized_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_mutations');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('finance_evidence');
        Schema::dropIfExists('finance_transaction_items');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_budgets');
        Schema::dropIfExists('finance_accounts');
    }
};
