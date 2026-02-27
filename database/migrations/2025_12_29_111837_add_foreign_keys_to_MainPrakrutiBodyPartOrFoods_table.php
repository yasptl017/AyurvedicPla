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
        Schema::table('MainPrakrutiBodyPartOrFoods', function (Blueprint $table) {
            $table->foreign(['BodyPartOrFoodId'])->references(['Id'])->on('BodyPartOrFoods')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['MainPrakrutiId'])->references(['Id'])->on('MainPrakrutis')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('MainPrakrutiBodyPartOrFoods', function (Blueprint $table) {
            $table->dropForeign('mainprakrutibodypartorfoods_bodypartorfoodid_foreign');
            $table->dropForeign('mainprakrutibodypartorfoods_mainprakrutiid_foreign');
        });
    }
};
