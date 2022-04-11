<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPriceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_settings', function (Blueprint $table) {
            $table->increments('id');       
            $table->decimal('cours_usd',20,2)->nullable();
            $table->decimal('cours_rmb',20,2)->nullable();
            $table->decimal('frais_taxe_usd_bateau',20,2)->nullable();
            $table->decimal('frais_taxe_usd_avion',20,2)->nullable();
            $table->decimal('frais_usd_bateau',20,2)->nullable();
            $table->decimal('frais_compagnie_usd_bateau',20,2)->nullable();
            $table->decimal('constante_taxe',20,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_price_settings');
    }
}
