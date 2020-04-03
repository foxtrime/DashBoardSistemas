<?php

use Illuminate\Database\Seeder;

class ServidorTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//factory(App\Models\Servidor::class,10)->create();

		DB::table('servidores')->insert(['nome' => 'NÃO INFORMADO', 					'motivo' => 'CLINICO GERAL']);
		DB::table('servidores')->insert(['nome' => 'ADRIANA OLIVEIRA DA SILVA', 		'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'ALEXANDRE FILGUEIRA MENDONÇA', 		'motivo' => 'FISIOTERAPEUTA']);
		DB::table('servidores')->insert(['nome' => 'ANA PAULA PEREIRA LIRA', 			'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'ANDERSON DE PINHO RICETTE COSTA', 	'motivo' => 'FISIOTERAPEUTA']);
		DB::table('servidores')->insert(['nome' => 'CARLA GOMES', 						'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'CAROLAINE DUARTE MELO SILVA', 		'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'CLAUDIO WIENEN MENDITI', 			'motivo' => 'MÉDICO']);
		DB::table('servidores')->insert(['nome' => 'EDILENE GOMES MARIA DA SILVA', 		'motivo' => 'NUTRICIONISTA']);
		DB::table('servidores')->insert(['nome' => 'EDNA MARIA AGNELLO NOGUEIRA', 		'motivo' => 'FONOÁUDIOLOGO']);
		DB::table('servidores')->insert(['nome' => 'ELAINE MOUTINHO M. DE OLIVEIRA', 	'motivo' => 'MÉDICO']);
		DB::table('servidores')->insert(['nome' => 'JACQUELINE ADELAIDE DOREA', 		'motivo' => 'ASSITENTE SOCIAL']);
		DB::table('servidores')->insert(['nome' => 'JAQUELINE REGINA PENHA DA SILVA', 	'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'JUSSARA PENA DOS S. DE OLIVEIRA', 	'motivo' => 'PSICÓLOGO']);
		DB::table('servidores')->insert(['nome' => 'LAIZ APARECIDA COELHO TRAJANO', 	'motivo' => 'TÉC. EM ENFERMAGEM']);
		DB::table('servidores')->insert(['nome' => 'LIVIA VIEIRA DIAS', 				'motivo' => 'FISIOTERAPEUTA']);
		DB::table('servidores')->insert(['nome' => 'LORRANE MARTINS DE O. DA SILVA', 	'motivo' => 'ODONTÓLOGO']);
		DB::table('servidores')->insert(['nome' => 'LUCIA MARIA NOBERTO DA SILVA', 		'motivo' => 'ENFERMEIRO']);
		DB::table('servidores')->insert(['nome' => 'REGINA CELI TAVARES B. RODRIGUES', 	'motivo' => 'ASSITENTE SOCIAL']);
		DB::table('servidores')->insert(['nome' => 'ROGER SOARES DE OLIVEIRA', 			'motivo' => 'FISIOTERAPEUTA']);
		DB::table('servidores')->insert(['nome' => 'VÂNIA LÚCIA SOUSA DOS S. BARBOSA', 	'motivo' => 'ENFERMEIRO']);



	}
}
