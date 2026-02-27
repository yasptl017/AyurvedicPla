<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CalendarAppointments', function (Blueprint $table) {
            $table->uuid('Id');
            $table->string('Title', 450);
            $table->text('Description')->nullable();
            $table->dateTime('StartDate');
            $table->dateTime('EndDate')->nullable();
            $table->boolean('AllDay')->default(true);
            $table->bigInteger('ClinicId')->nullable()->index();

            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy')->nullable();
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable()->default(false);

            $table->primary(['Id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CalendarAppointments');
    }
};
