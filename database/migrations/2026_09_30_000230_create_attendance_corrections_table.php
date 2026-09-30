<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('requested_status',40);
            $table->time('requested_time_in')->nullable();
            $table->time('requested_time_out')->nullable();
            $table->text('reason');
            $table->enum('status',['PENDING','APPROVED','REJECTED'])->default('PENDING')->index();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->index(['employee_id','status']);
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_corrections'); }
};
