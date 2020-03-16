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
        $resultados = $this->resultados()->whereNotIn('id', $resultadoIds)->get()->pluck('id');
        if(count($resultados)>0){
            $noProgramados = new DuracionResultado();
            $noProgramados->resultado = $resultados;
            return $duracionResultado->push($noProgramados);
        }else{
            return $duracionResultado;
        }
    }

    protected $fillable = [
        'nombre',
        'tipo',
        'programa_id',
        'codigo'
    ];
}
