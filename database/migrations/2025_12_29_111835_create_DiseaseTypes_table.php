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
        Schema::create('DiseaseTypes', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');
            $table->text('Name')->nullable();
            $table->text('Description')->nullable();
            $table->unsignedInteger('DiseaseId');
            $table->boolean('IsSpecial')->default(false);
            $table->text('Do')->nullable();
            $table->text('Dont')->nullable();
            $table->text('Prognosis')->nullable();
            $table->integer('OrderNumber')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DiseaseTypes');
    }
};
