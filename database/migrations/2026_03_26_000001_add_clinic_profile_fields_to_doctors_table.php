<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->text('Logo')->nullable()->after('Address');
            $table->text('DoctorName')->nullable()->after('Logo');
            $table->text('RegistrationNo')->nullable()->after('DoctorName');
            $table->text('MobileNo2')->nullable()->after('MobileNo');
            $table->text('WarningField1')->nullable()->after('RegistrationNo');
            $table->text('WarningField2')->nullable()->after('WarningField1');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn([
                'Logo',
                'DoctorName',
                'RegistrationNo',
                'MobileNo2',
                'WarningField1',
                'WarningField2',
            ]);
        });
    }
};
