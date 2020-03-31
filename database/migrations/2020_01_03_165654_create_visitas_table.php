<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('acompanhamento_id')         ->unsigned();
           
            $table->enum('motivo',[  
                'ATENDIMENTO',
                'APOIO',
                'ATENDIMENTO E APOIO',
                'CURATIVO',
                'COLETA',
                'VACINA',
                'ENTREGA DE MEDICAÇÃO',
                'ENTREGA DE INSUMOS',

            ])          ->default('ATENDIMENTO');

            $table->enum('situacao',[   

                'REALIZADA',
                'REALIZADA - ALTA',
                
                'NÃO REALIZADA - EQUIPE NÃO ATENDIDA',
                'NÃO REALIZADA - PACIENTE DESACOMPANHADO',
                'NÃO REALIZADA - PACIENTE RECUSOU',
                'NÃO REALIZADA - INTERNADO',
                'NÃO REALIZADA - PACIENTE AUSENTE',
                'NÃO REALIZADA - OUTROS',
                
                'ENCERRAMENTO - SEM PERFIL',
                'ENCERRAMENTO - MUDANÇA DE MUNICÍPIO', 
                'ENCERRAMENTO - ALTA ADMINISTRATIVA',
                'ENCERRAMENTO - ÓBITO',

            ])             ->default('REALIZADA');

            $table->enum('tp_atendimento',[   
                'FISIOTERAPIA',
                'NUTRIÇÃO',
                'MEDICINA',
                'PSICOLOGIA',
                'SERVIÇO SOCIAL',
                'ODONTOLOGIA',
                'FONOAUDIOLOGIA',
                'ENFERMAGEM',
            ])             ->default('MEDICINA');

            $table->date('dt_visita');
            $table->text('observacao')                  ->nullable(); 
            
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('visitas', function($table){
            $table->foreign('acompanhamento_id')->references('id')->on('acompanhamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitas');
    }
}
