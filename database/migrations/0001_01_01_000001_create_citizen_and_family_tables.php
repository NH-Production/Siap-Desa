<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('nik', 20)->index();
            $table->string('no_kk', 20)->nullable()->index();
            $table->string('name');
            $table->string('birth_place', 80)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 15); // LAKI_LAKI, PEREMPUAN
            $table->string('blood_type', 5)->nullable();
            $table->string('religion', 25)->nullable();
            $table->string('marital_status', 25)->nullable();
            $table->string('occupation', 80)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('citizenship', 10)->default('WNI');
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('village_name', 80)->nullable();
            $table->string('district_name', 80)->nullable();
            $table->string('regency_name', 80)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('status', 20)->default('TETAP'); // TETAP, PINDAH, MENINGGAL, SEMENTARA
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            
            // Sync & Version tracking
            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id');
            $table->string('no_kk', 20)->unique();
            $table->string('head_nik', 20)->nullable();
            $table->string('head_name');
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->date('issue_date')->nullable();
            $table->text('notes')->nullable();

            $table->integer('version')->default(1);
            $table->uuid('last_modified_by')->nullable();
            $table->uuid('last_modified_device_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->foreignId('citizen_id')->constrained('citizens')->cascadeOnDelete();
            $table->string('relation_status', 30); // KEPALA_KELUARGA, SUAMI, ISTRI, ANAK, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('families');
        Schema::dropIfExists('citizens');
    }
};
