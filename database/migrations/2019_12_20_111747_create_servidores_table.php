<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServidoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servidores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome',100);

            $table->enum('motivo',[
                'CLINICO GERAL',
                'TÉC. EM ENFERMAGEM',
                'FISIOTERAPEUTA',
                'MÉDICO',
                'NUTRICIONISTA',
                'FONOÁUDIOLOGO',
                'MÉDICO',
                'PSICÓLOGO',
                'ODONTÓLOGO',
                'ASSITENTE SOCIAL',
                'ENFERMEIRO',
            ])          ->default('ENFERMEIRO');
            $table->boolean('ativo')->default('1');
            $table->softDeletes();
            
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
        Schema::dropIfExists('servidores');
    }
}
