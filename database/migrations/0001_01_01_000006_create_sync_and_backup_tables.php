<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('device_id')->nullable();
            $table->uuid('village_id');
            $table->string('table_name', 80);
            $table->uuid('record_uuid');
            $table->string('operation', 20); // INSERT, UPDATE, DELETE
            $table->json('payload');
            $table->integer('base_version')->default(1);
            $table->integer('local_version')->default(1);
            $table->string('status', 20)->default('PENDING'); // PENDING, PROCESSING, SYNCED, FAILED, CONFLICT, CANCELLED
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('sync_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('change_uuid')->unique();
            $table->uuid('village_id');
            $table->uuid('source_device_id')->nullable();
            $table->string('table_name', 80);
            $table->uuid('record_uuid');
            $table->string('operation', 20);
            $table->integer('version')->default(1);
            $table->json('payload');
            $table->timestamps();
        });

        Schema::create('sync_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_uuid')->unique();
            $table->uuid('device_id')->nullable();
            $table->uuid('village_id');
            $table->integer('total_changes')->default(0);
            $table->string('status', 20)->default('SUCCESS');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sync_conflicts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('table_name', 80);
            $table->uuid('record_uuid');
            $table->uuid('device_id')->nullable();
            $table->integer('server_version')->default(1);
            $table->integer('local_version')->default(1);
            $table->json('server_payload');
            $table->json('local_payload');
            $table->string('resolution', 30)->nullable(); // SERVER_WINS, LOCAL_WINS, MERGED, MANUAL
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sync_cursors', function (Blueprint $table) {
            $table->id();
            $table->uuid('device_id')->unique();
            $table->bigInteger('last_pull_change_id')->default(0);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->bigInteger('size_bytes')->default(0);
            $table->string('type', 30)->default('MANUAL'); // MANUAL, AUTOMATIC, PRE_UPDATE
            $table->string('status', 20)->default('SUCCESS');
            $table->string('checksum', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('update_logs', function (Blueprint $table) {
            $table->id();
            $table->string('app_version_from')->nullable();
            $table->string('app_version_to');
            $table->integer('schema_from')->nullable();
            $table->integer('schema_to');
            $table->string('status', 20)->default('SUCCESS');
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('update_logs');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('sync_cursors');
        Schema::dropIfExists('sync_conflicts');
        Schema::dropIfExists('sync_batches');
        Schema::dropIfExists('sync_changes');
        Schema::dropIfExists('sync_queue');
    }
};
