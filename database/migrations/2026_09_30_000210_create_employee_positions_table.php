<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('position',100);
            $table->string('department',100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(true);
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->index(['employee_id','is_current']);
        });
    }

    public function down(): void { Schema::dropIfExists('employee_positions'); }
};
