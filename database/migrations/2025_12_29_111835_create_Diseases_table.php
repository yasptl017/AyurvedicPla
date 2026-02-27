<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Diseases', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');
            $table->string('Name', 450)->nullable();
            $table->longText('Introduction')->nullable();
            $table->longText('Purvaroopa')->nullable();
            $table->longText('DoDont')->nullable();
            $table->longText('Sadhyabadyatva')->nullable();
            $table->longText('ChikitsaSutra')->nullable();
            $table->longText('Samprapti')->nullable();
            $table->longText('Upadrava')->nullable();
            $table->longText('Panchakarma')->nullable();
            $table->longText('Causes')->nullable();
            $table->longText('ArishtaLaxana')->nullable();
            $table->longText('DifferentialDiagnosis')->nullable();
            $table->longText('LaboratoryInvestions')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Diseases');
    }
};
