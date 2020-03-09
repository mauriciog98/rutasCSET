<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Programa extends Model
{
    use SoftDeletes;

    public function nivelFormacion()
    {
        return $this->belongsTo('App\NivelFormacion');
    }

    public function red()
    {
        return $this->belongsTo('App\Red');
    }

    public function competencias()
    {
        return $this->hasMany('App\Competencia');
    }

    public function resultados()
    {
        return $this->hasManyThrough('App\Resultado','App\Competencia');
    }

    public function transversales()
    {
        //en caso de error añadir al final 'programa_id','competencia_id'
        return $this->belongsToMany('App\Competencia','transversales');
    }

    protected $fillable = [
        'codigo',
        'version',
        'nombre',
        'nivel_formacion_id',
        'duracion',
        'duracion_lectiva',
        'red_id',
        'estado',
        'ruta'
    ];
}
