<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTransactionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_transaction_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_transaction_id');
            $table->foreign('package_transaction_id')->references('id')->on('package_transactions')->onDelete('cascade');
            $table->unsignedInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->decimal('price', 22, 4)->nullable();
          
            $table->decimal('qte', 22, 4)->default(0);
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
        Schema::dropIfExists('package_transaction_lines');
    }
}
