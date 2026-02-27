<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sketches', function (Blueprint $table) {
            $table->longText('sketch')->change();
        });
    }

    public function down(): void
    {
    }
};
