<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('village_id')->index();
            $table->string('no_kk', 16)->unique();
            $table->string('head_name', 150);
            $table->string('head_nik', 16)->nullable()->index();
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->date('issue_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['village_id','head_name']);
        });
    }

    public function down(): void { Schema::dropIfExists('families'); }
};
