<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('num_documento', 20)->unique();
            $table->string('primernombre', 50)->nullable(false);
            $table->string('segundonombre', 50)->nullable(false);
            $table->string('apellidos', 100)->nullable(false);
            $table->string('direccion', 70)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('ciudad', 50)->nullable();
            
            $table->timestamps();
        });
        
        DB::table('personas')->insert(array('num_documento'=>'123456789','primernombre'=>'PEPITO','segundonombre'=>'MANUEL', 
        'apellidos'=>'PEREZ', 'direccion'=>'Cali - Valle', 'telefono'=>'3154802815'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
