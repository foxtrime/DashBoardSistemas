<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServidorVisitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servidor_visita', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('servidor_id')->unsigned();
            $table->bigInteger('visita_id')->unsigned();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('servidor_visita', function($table){
            $table->foreign('servidor_id')->references('id')->on('servidores')->onDelete('cascade');
            $table->foreign('visita_id')->references('id')->on('visitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servidor_visita');
    }
}
