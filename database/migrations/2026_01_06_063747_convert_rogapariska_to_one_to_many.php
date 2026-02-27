<?php

use App\Models\PatientHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('RogaPariksas', function (Blueprint $table) {
            $table->foreignIdFor(PatientHistory::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('RogaPariksas', function (Blueprint $table) {
            $table->dropForeignIdFor(PatientHistory::class);
        });
    }
};
