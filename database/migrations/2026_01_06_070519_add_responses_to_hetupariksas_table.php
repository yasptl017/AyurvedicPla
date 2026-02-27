<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('HetuPariksas', function (Blueprint $table) {
            $table->jsonb('Responses')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('HetuPariksas', function (Blueprint $table) {
            $table->dropColumn('Responses');
        });
    }
};
