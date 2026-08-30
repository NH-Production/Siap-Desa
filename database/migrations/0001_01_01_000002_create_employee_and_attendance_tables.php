<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('nip', 30)->nullable();
            $table->string('name');
            $table->string('position', 80); // Kepala Desa, Sekretaris Desa, Kaur Keuangan, dll
            $table->string('department', 80)->nullable();
            $table->string('employment_status', 30)->default('PERANGKAT_DESA');
            $table->date('join_date')->nullable();
            $table->string('qr_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->string('photo_path')->nullable();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('title', 80);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('decree_number', 80)->nullable(); // Nomor SK
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->uuid('device_id')->nullable();
            $table->date('date')->index();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('status', 25)->default('HADIR'); // HADIR, TERLAMBAT, PULANG_CEPAT, IZIN, SAKIT, DINAS_LUAR, ALPHA
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('requested_status', 25);
            $table->time('requested_time_in')->nullable();
            $table->time('requested_time_out')->nullable();
            $table->text('reason');
            $table->string('status', 20)->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('employee_positions');
        Schema::dropIfExists('employees');
    }
};
