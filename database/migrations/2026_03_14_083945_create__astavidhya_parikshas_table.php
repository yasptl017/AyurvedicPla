<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AstavidhyaParikshas', function (Blueprint $table) {
            $table->uuid('Id');
            $table->uuid('PatientHistoryId');

            // નાડી (Pulse)
            $table->string('Nadi')->nullable(); // vat, pit, kaf

            // મળ (Mala)
            $table->string('Mala')->nullable(); // kshina, aam

            // મલ (Mal - stool)
            $table->string('Mal')->nullable(); // soft, hard, ibs

            // મુત્ર (Mutra - urine)
            $table->text('Mutra')->nullable();

            // જીહવા (Jihva - tongue)
            $table->string('Jihva')->nullable(); // saam, niraam

            // સ્પર્શ (Sparsha - touch)
            $table->string('Sparsha')->nullable(); // ushna, sheet

            // ક્ષુધા (Kshudha - appetite)
            $table->string('Kshudha')->nullable(); // sam, visham, tikshna, mand

            // નિંદ્રા (Nindra - sleep)
            $table->string('Nindra')->nullable(); // samyak, madhyam, alpa

            // આર્તવ (Aartav - menstrual)
            $table->string('AartavType')->nullable(); // regular, irregular
            $table->string('AartavAmount')->nullable(); // scanty, moderate, excessive

            // Remark
            $table->text('Remark')->nullable();

            // Audit fields
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
        Schema::dropIfExists('AstavidhyaParikshas');
    }
};
