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
        Schema::create('PatientHistories', function (Blueprint $table) {
            $table->uuid('Id');
            $table->uuid('PatientId');
            $table->uuid('DoctorId')->nullable();
            $table->text('Remark')->nullable();
            $table->text('Note')->nullable();
            $table->float('ConsultationFee');
            $table->float('MedicinesFee');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy')->nullable();
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable()->default(false);
            $table->dateTime('NextAppointmentDate')->nullable();
            $table->boolean('IsHetuPariksa')->default(false);
            $table->boolean('IsLaboratoryReport')->default(false);
            $table->boolean('IsPanchakarma')->default(false);
            $table->boolean('IsRogaPariksa')->default(false);
            $table->boolean('IsVital')->default(false);
            $table->boolean('IsWomenHistory')->default(false);
            $table->boolean('IsImages')->default(false);

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientHistories');
    }
};
