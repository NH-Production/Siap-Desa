<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add client_id to central_villages & central_licenses if not exists
        Schema::table('central_villages', function (Blueprint $table) {
            if (!Schema::hasColumn('central_villages', 'client_id')) {
                $table->string('client_id', 80)->nullable()->unique()->after('uuid');
            }
        });

        Schema::table('central_licenses', function (Blueprint $table) {
            if (!Schema::hasColumn('central_licenses', 'locked_client_id')) {
                $table->string('locked_client_id', 80)->nullable()->after('license_key');
            }
        });

        // 2. Central Server Global Settings Table
        if (!Schema::hasTable('central_server_settings')) {
            Schema::create('central_server_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 80)->unique();
                $table->text('value')->nullable();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('central_server_settings');
        Schema::table('central_licenses', function (Blueprint $table) {
            $table->dropColumn('locked_client_id');
        });
        Schema::table('central_villages', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
};
