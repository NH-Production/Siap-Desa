<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id')->index();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('name',150)->index();
            $table->string('nip',30)->nullable()->index();
            $table->string('position',100)->index();
            $table->string('department',100)->nullable();
            $table->string('employment_status',40)->default('TETAP');
            $table->date('join_date')->nullable();
            $table->string('qr_token',80)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('employees'); }
};
