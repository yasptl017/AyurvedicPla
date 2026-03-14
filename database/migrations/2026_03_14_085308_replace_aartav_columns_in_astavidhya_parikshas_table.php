<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('AstavidhyaParikshas', function (Blueprint $table) {
            $table->dropColumn(['AartavType', 'AartavAmount']);
            $table->string('AartavRegular')->nullable()->after('Nindra');
            $table->string('AartavIrregular')->nullable()->after('AartavRegular');
        });
    }

    public function down(): void
    {
        Schema::table('AstavidhyaParikshas', function (Blueprint $table) {
            $table->dropColumn(['AartavRegular', 'AartavIrregular']);
            $table->string('AartavType')->nullable()->after('Nindra');
            $table->string('AartavAmount')->nullable()->after('AartavType');
        });
    }
};
