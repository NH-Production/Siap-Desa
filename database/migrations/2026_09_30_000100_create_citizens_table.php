<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id')->index();
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16)->nullable()->index();
            $table->string('name', 150)->index();
            $table->string('birth_place', 80)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['LAKI_LAKI','PEREMPUAN']);
            $table->string('blood_type', 5)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('occupation', 80)->nullable();
            $table->string('education', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->enum('status', ['TETAP','PINDAH','MENINGGAL','SEMENTARA'])->default('TETAP')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['village_id','name']);
            $table->index(['village_id','status']);
        });
    }

    public function down(): void { Schema::dropIfExists('citizens'); }
};
