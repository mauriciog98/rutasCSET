<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('numero');
            $table->unsignedBigInteger('programa_id');
            $table->date('fecha_inicio');
            $table->date('fecha_lectiva');
            $table->date('fecha_productiva')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->jsonb('ruta')->nullable();
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
        Schema::dropIfExists('fichas');
    }
}
