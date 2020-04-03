<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcompanhamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acompanhamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('ativo')                                        ->default('1');

            $table->enum('atendimento_domiciliar',['AD1','AD2','AD3'])      ->nullable()->default('AD1');
            $table->enum('ad_admissao',['AD1','AD2','AD3'])                 ->nullable();

            $table->enum('origem', ['HOSPITAL','EMERGÊNCIA','ESPONTÂNEA','ATENÇÃO BÁSICA'])  ->nullable()->default('HOSPITAL');
            $table->enum('base',['JACUTINGA','PARANÁ'])                     ->nullable()->default('JACUTINGA');
            $table->string('situacao',30)                                   ->default('-----');
           
            $table->boolean('medicina')                                     ->default('0');
            $table->boolean('enfermagem')                                   ->default('0');
            $table->boolean('fisioterapia')                                 ->default('0');
            $table->boolean('fonoaudiologia')                               ->default('0');
            $table->boolean('nutricao')                                     ->default('0');
            $table->boolean('psicologia')                                   ->default('0');
            $table->boolean('odontologia')                                  ->default('0');
            $table->boolean('servico_social')                               ->default('0');

            $table->text('observacao')     			                        ->nullable(); 

            $table->bigInteger('paciente_id')                               ->unsigned();
            $table->bigInteger('cid_id')                                    ->unsigned();
            $table->date('dt_inicio')                                       ->nullable();
            $table->date('dt_termino')                                      ->nullable();
            $table->string('destino_pos_alta',200)                          ->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('acompanhamentos', function($table){
            $table->foreign('paciente_id')	 ->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('cid_id')	 ->references('id')->on('cids')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acompanhamentos');
    }
}
