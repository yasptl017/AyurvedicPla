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
        Schema::create('RogaPariksas', function (Blueprint $table) {
            $table->uuid('Id');
            $table->text('Agni')->nullable();
            $table->text('Udbhavasthana')->nullable();
            $table->text('Adhishthana')->nullable();
            $table->text('Vyadhi_swarupa1')->nullable();
            $table->text('Vyadhi_swarupa2')->nullable();
            $table->text('Vyadhi_swarupa3')->nullable();
            $table->text('Nidaana')->nullable();
            $table->text('Poorvarupa')->nullable();
            $table->text('Roopa')->nullable();
            $table->text('Upashaya')->nullable();
            $table->text('Anupashaya')->nullable();
            $table->text('Sampraapti')->nullable();
            $table->text('Sambhavitha_vyadhi')->nullable();
            $table->text('Rogavinischaya')->nullable();
            $table->text('Vyadhi_avastha1')->nullable();
            $table->text('Vyadhi_avastha2')->nullable();
            $table->text('Prognosis')->nullable();
            $table->text('Upadrava')->nullable();
            $table->text('Nidana')->nullable();
            $table->boolean('Vat')->nullable();
            $table->boolean('Pit')->nullable();
            $table->boolean('Kaf')->nullable();
            $table->boolean('Rasa')->nullable();
            $table->boolean('Rakta')->nullable();
            $table->boolean('Mansa')->nullable();
            $table->boolean('Meda')->nullable();
            $table->boolean('Asthi')->nullable();
            $table->boolean('Majja')->nullable();
            $table->boolean('Shukra')->nullable();
            $table->boolean('Stanya')->nullable();
            $table->boolean('Raja')->nullable();
            $table->boolean('Kandara')->nullable();
            $table->boolean('Sira')->nullable();
            $table->boolean('Dhamani')->nullable();
            $table->boolean('Twacha')->nullable();
            $table->boolean('Snau')->nullable();
            $table->boolean('Poorisha')->nullable();
            $table->boolean('Mootra')->nullable();
            $table->boolean('Sweda')->nullable();
            $table->boolean('Kapha')->nullable();
            $table->boolean('Pitta')->nullable();
            $table->boolean('Khamala')->nullable();
            $table->boolean('Kesha')->nullable();
            $table->boolean('Nakha')->nullable();
            $table->boolean('Akshisneha')->nullable();
            $table->boolean('Loma')->nullable();
            $table->boolean('Shmashru')->nullable();
            $table->boolean('Sanaga')->nullable();
            $table->boolean('Vimargagamana')->nullable();
            $table->boolean('Atipravrutti')->nullable();
            $table->boolean('Sira_granthi')->nullable();
            $table->boolean('Koshtha')->nullable();
            $table->boolean('Shakha')->nullable();
            $table->boolean('Marma')->nullable();
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RogaPariksas');
    }
};
