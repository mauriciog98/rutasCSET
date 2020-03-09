<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('codigo');
            $table->unsignedInteger('version');
            $table->string('nombre');
            $table->unsignedBigInteger('nivel_formacion_id');
            $table->unsignedInteger('duracion');
            $table->unsignedInteger('duracion_lectiva');
            $table->unsignedBigInteger('red_id');
            $table->boolean('estado')->nullable()->default(true);
            $table->jsonb('ruta')->nullable();
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
        Schema::dropIfExists('programas');
    }
}
