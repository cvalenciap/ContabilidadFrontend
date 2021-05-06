<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatrimonioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('patrimonio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number_inventory')->unsingneg()->nullable()->default('');
            $table->string('code')->unsingneg()->nullable()->default('');
            $table->string('description')->unsingneg()->nullable()->default('');
            $table->string('serial')->unsingneg()->nullable()->default('');
            $table->string('model')->unsingneg()->nullable()->default('');
            $table->string('brand')->unsingneg()->nullable()->default('');
            $table->string('color')->unsingneg()->nullable()->default('');
            $table->string('type')->unsingneg()->nullable()->default('');
            $table->string('state')->unsingneg()->nullable()->default('');
            $table->string('place')->unsingneg()->nullable()->default('');
            $table->integer('periodo_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists('patrimonio');
    }
}
