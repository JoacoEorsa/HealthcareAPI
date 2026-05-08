<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_clinic', function (Blueprint $table) {
            $table->foreignId('doctor_id')->constrained(table: 'doctors');
            $table->foreignId('clinic_id')->constrained(table: 'clinics');
            $table->primary(['doctor_id', 'clinic_id']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_clinic');
    }
};
