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
        Schema::create('AspNetUsers', function (Blueprint $table) {
            $table->string('Id', 450);
            $table->string('UserName', 256)->nullable();
            $table->string('NormalizedUserName', 256)->nullable();
            $table->string('Email', 256)->nullable();
            $table->string('NormalizedEmail', 256)->nullable();
            $table->text('Password')->nullable();
            $table->text('SecurityStamp')->nullable();
            $table->text('ConcurrencyStamp')->nullable();
            $table->text('PhoneNumber')->nullable();
            $table->dateTimeTz('LockoutEnd')->nullable();
            $table->text('FirstName')->nullable();
            $table->text('LastName')->nullable();
            $table->boolean('IsAdmin');
            $table->string('remember_token', 100)->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AspNetUsers');
    }
};
