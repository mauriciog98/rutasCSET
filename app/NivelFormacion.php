<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NivelFormacion extends Model
{
    protected $table = 'niveles_formacion';

    public function programas()
    {
        return $this->belongsTo('App\Programa');
    }

    public function fichas()
    {
        return $this->hasManyThrough('App\Fichas','App\Programa');
    }

    protected $fillable = [
      'nombre'
    ];
}
