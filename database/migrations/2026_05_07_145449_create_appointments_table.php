<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained(table: 'doctors');
            $table->foreignId('patient_id')->constrained(table: 'patients');
            $table->foreignId('clinic_id')->constrained(table: 'clinics');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status')->default('scheduled');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
