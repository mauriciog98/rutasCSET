<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $hidden = ['competencia_id'];
    public function competencia()
    {
        $this->belongsTo('App\Competencia');
    }

    public function getProgramadoAttribute(){
        return true;
    }

    protected $fillable = [
        'codigo',
        'nombre',
        'competencia_id'
    ];
}
