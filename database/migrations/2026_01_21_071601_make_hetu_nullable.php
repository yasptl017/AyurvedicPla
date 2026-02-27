<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('HetuPariksas', function (Blueprint $table) {
            $table->uuid('DeletedBy')->nullable()->change();
            $table->boolean('IsDeleted')->nullable()->change();

        });
    }

};
