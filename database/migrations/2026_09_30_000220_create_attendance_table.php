<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id')->index();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->uuid('device_id')->nullable()->index();
            $table->date('date')->index();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status',['HADIR','TERLAMBAT','PULANG_CEPAT','IZIN','SAKIT','DINAS_LUAR','ALPHA'])->default('HADIR');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['employee_id','date']);
            $table->index(['village_id','date']);
        });
    }

    public function down(): void { Schema::dropIfExists('attendance'); }
};
