<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Central Villages (Tenants)
        Schema::create('central_villages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->string('district', 100);
            $table->string('regency', 100);
            $table->string('province', 100);
            $table->string('head_name', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('status', 20)->default('ACTIVE'); // ACTIVE, TRIAL, SUSPENDED, EXPIRED
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Central Licenses
        Schema::create('central_licenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('central_village_id')->constrained('central_villages')->cascadeOnDelete();
            $table->string('license_key', 80)->unique();
            $table->string('tier', 30)->default('ENTERPRISE'); // TRIAL, STANDARD, ENTERPRISE
            $table->integer('max_devices')->default(5);
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->string('status', 20)->default('ACTIVE'); // ACTIVE, REVOKED, EXPIRED
            $table->text('allowed_features')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Central Devices (Registered Client Machines)
        Schema::create('central_devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('central_license_id')->constrained('central_licenses')->cascadeOnDelete();
            $table->string('device_code', 50);
            $table->string('device_name', 100)->nullable();
            $table->string('fingerprint', 128)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('app_version', 20)->default('1.0.0');
            $table->timestamp('last_seen_at')->nullable();
            $table->string('status', 20)->default('ACTIVE'); // ACTIVE, BLOCKED
            $table->timestamps();
        });

        // 4. Central Sync Logs & Ingestion Telemetry
        Schema::create('central_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('village_code', 30);
            $table->string('device_code', 50);
            $table->string('direction', 10); // PUSH, PULL
            $table->integer('records_count')->default(0);
            $table->string('status', 20)->default('SUCCESS'); // SUCCESS, FAILED, CONFLICT
            $table->text('details')->nullable();
            $table->integer('latency_ms')->default(0);
            $table->timestamps();
        });

        // 5. Central Releases & OTA Patch Hub
        Schema::create('central_releases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('version', 20)->unique(); // 1.0.1, 1.0.2
            $table->integer('schema_version')->default(1);
            $table->string('title', 150);
            $table->text('changelog')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->default(0);
            $table->string('checksum_sha256', 64)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_published')->default(true);
            $table->date('release_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_releases');
        Schema::dropIfExists('central_sync_logs');
        Schema::dropIfExists('central_devices');
        Schema::dropIfExists('central_licenses');
        Schema::dropIfExists('central_villages');
    }
};
