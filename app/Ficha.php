<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    public function programa()
    {
        return $this->belongsTo('App\Programa');
    }

    public function red(){
        return $this->hasOneThrough('App\Red','App\Programa');
    }

    public function competencias()
    {
        return $this->programa()->competencias;
    }

    public function transversales()
    {
        return $this->programa()->transversales;
    }

    public function instructor(){
        return $this->belongsTo('App\User');
    }

    protected $fillable = [
        'numero',
        'programa_id',
        'fecha_inicio',
        'fecha_lectiva',
        'fecha_productiva',
        'instructor_id',
        'ruta'
    ];
}
