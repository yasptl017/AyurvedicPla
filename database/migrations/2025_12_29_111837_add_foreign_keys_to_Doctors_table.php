<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Doctors', function (Blueprint $table) {
            $table->foreign(['CityId'])->references(['Id'])->on('Cities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['StateId'])->references(['Id'])->on('States')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Doctors', function (Blueprint $table) {
            $table->dropForeign('doctors_cityid_foreign');
            $table->dropForeign('doctors_stateid_foreign');
        });
    }
};
