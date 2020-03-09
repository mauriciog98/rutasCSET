<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Red extends Model
{
    protected $table = "redes";

    public function programas()
    {
        return $this->hasMany('App\Programa');
    }

    public function lider(){
        return $this->belongsTo('App\User');
    }

    public function instructores()
    {
        return $this->hasMany('App\User','red_id');
    }

    protected $fillable = [
        'nombre',
        'lider_id'
    ];
}
