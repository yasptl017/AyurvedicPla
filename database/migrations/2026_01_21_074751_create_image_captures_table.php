<?php

use App\Models\Patient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('image_captures', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class)->constrained('Patients');
            $table->string('capture');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_captures');
    }
};
