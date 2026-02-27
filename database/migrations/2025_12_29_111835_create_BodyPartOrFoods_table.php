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
        Schema::create('BodyPartOrFoods', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');
            $table->text('Name')->nullable();
            $table->text('Description')->nullable();
            $table->integer('OrderNumber')->default(0);

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BodyPartOrFoods');
    }
};
