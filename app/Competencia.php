<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    protected $hidden = ['competencia_id'];

    public function resultados()
    {
        return $this->hasMany('App\Resultado');
    }

    public function competencia()
    {
        return $this->belongsTo('App\Competencia');
    }

    public function duracionResultado()
    {
        return $this->hasMany('App\DuracionResultado');
    }

    public function getProgramacionResultadosAttribute()
    {
        $duracionResultado = $this->duracionResultado()->get();
        $resultadoIds = $duracionResultado->pluck('resultado')->collapse();
        $noProgramados = new DuracionResultado();
        $noProgramados->resultado = $this->resultados()->whereNotIn('id', $resultadoIds)->get()->pluck('id');
        $noProgramados->resultados_count = 1;
        return $duracionResultado->push($noProgramados) ;
    }

    protected $fillable = [
        'nombre',
        'tipo',
        'programa_id',
        'codigo'
    ];
}
