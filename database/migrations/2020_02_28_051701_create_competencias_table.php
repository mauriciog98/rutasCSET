<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('codigo');
            $table->string('nombre');
            $table->unsignedTinyInteger('tipo')->nullable()->default('1');
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->timestamps();
        });
        Schema::create('transversales', function (Blueprint $table) {
            $table->unsignedBigInteger('competencia_id');
            $table->unsignedBigInteger('programa_id');
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
        Schema::dropIfExists('competencias');
    }
}
