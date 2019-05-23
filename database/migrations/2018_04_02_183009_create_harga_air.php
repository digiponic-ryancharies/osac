<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHargaAir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_hargaair', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sumber_air');
            $table->float('batas_atas',8,2);
            $table->float('batas_bawah',8,2);
            $table->integer('harga');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_hargaair');
    }
}
