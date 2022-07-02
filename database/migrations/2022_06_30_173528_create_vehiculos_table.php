<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('id')->references('id')->on('personas')->onDelete('cascade');


            $table->string('placa', 6)->unique();
            $table->string('color', 50)->nullable(false);
            $table->string('marca', 50)->nullable(false);
            $table->string('tipo', 10)->nullable(false);

            $table->integer('conductor')->unsigned()->nullable();
            $table->foreign('conductor')->references('id')->on('personas');
            $table->integer('propietario')->unsigned()->nullable();
            $table->foreign('propietario')->references('id')->on('personas');

            $table->timestamps();
        });

        DB::table('vehiculos')->insert(array('id'=>'1', 'placa'=>'ABC123', 'color'=>'Verde-Blanco',
        'marca'=>'CHEVROLET', 'tipo'=>'PARTICULAR', 'conductor'=>'1', 'propietario'=>'1'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
}
