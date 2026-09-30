<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->foreignId('citizen_id')->constrained('citizens')->cascadeOnDelete();
            $table->string('relation_status', 30);
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->unique(['family_id','citizen_id']);
            $table->index('relation_status');
        });
    }

    public function down(): void { Schema::dropIfExists('family_members'); }
};
