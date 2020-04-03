<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('dt_cadastro')                                     ->nullable();
            $table->bigInteger('prontuario')                                ->unsigned();
            
            $table->string('situacao',30)                                   ->default('-----');

            $table->char('cpf',14)                                          ->nullable();
            $table->string('identidade',20)                                 ->nullable();
            $table->string('org_exp_idt',10)                                ->nullable();

            $table->string('nome',100);
            $table->enum('sexo',['F', 'M']);
            $table->date('nascimento')                                      ->nullable();
            $table->string('sus',19)                                        ->nullable();
			$table->string('bairro',30)                                     ->nullable();
			$table->string('logradouro',100);
			$table->string('numero',10)                                     ->nullable();
			$table->string('complemento',100)                               ->nullable();
            $table->char('cep',10)                                          ->nullable();
            $table->string('telefone1',15)                                  ->nullable();
            $table->string('telefone2',15)                                  ->nullable();
            $table->string('telefone3',15)                                  ->nullable();

            $table->string('cuidador',100)                                  ->nullable();

            $table->boolean('internado')                                    ->nullable()->default('0');
            $table->boolean('obito')                                        ->nullable()->default('0');
            $table->date('dt_obito')                                        ->nullable();
            $table->text('observacao')             			                ->nullable(); 
            
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
        Schema::dropIfExists('pacientes');
    }
}
