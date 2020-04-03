<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                 
        $this->call(CidTableSeeder::class);
        $this->call(ServidorTableSeeder::class);
        //$this->call(PacienteTableSeeder::class);
        //$this->call(VisitaTableSeeder::class);

    }
}
