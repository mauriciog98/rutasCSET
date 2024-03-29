<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDuracionResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duracion_resultados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->jsonb('resultado');
            $table->unsignedBigInteger('competencia_id');
            $table->unsignedInteger('duracion');
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
        Schema::dropIfExists('duracion_resultados');
    }
}
