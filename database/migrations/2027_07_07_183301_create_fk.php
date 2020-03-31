<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFk extends Migration
{
	public function up()
	{
		Schema::enableForeignKeyConstraints();
	}

	public function down()
	{
	
		Schema::disableForeignKeyConstraints();
	}
}