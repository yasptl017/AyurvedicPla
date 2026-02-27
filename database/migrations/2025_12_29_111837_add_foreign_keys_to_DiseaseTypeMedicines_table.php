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
        Schema::table('DiseaseTypeMedicines', function (Blueprint $table) {
            $table->foreign(['AnupanaId'])->references(['Id'])->on('Anupanas')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['DiseaseTypeId'])->references(['Id'])->on('DiseaseTypes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['MedicineId'])->references(['Id'])->on('Medicines')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['TimeOfAdministrationId'])->references(['Id'])->on('TimeOfAdministrations')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DiseaseTypeMedicines', function (Blueprint $table) {
            $table->dropForeign('diseasetypemedicines_anupanaid_foreign');
            $table->dropForeign('diseasetypemedicines_diseasetypeid_foreign');
            $table->dropForeign('diseasetypemedicines_medicineid_foreign');
            $table->dropForeign('diseasetypemedicines_timeofadministrationid_foreign');
        });
    }
};
