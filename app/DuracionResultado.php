<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DuracionResultado extends Model
{
    protected $primaryKey = false;

    public $incrementing = false;

    protected $appends = ['resultados'];

    protected $hidden = ['competencia_id'];

    public function competencia()
    {
        return $this->belongsTo('App\Competencia');
    }

    public function getResultadosAttribute()
    {
        return Resultado::whereIn('id',$this->resultado)->get();
    }

    protected $casts = [
        'resultado' => 'array',
    ];

    protected $fillable = [
        'competencia_id',
        'resultado',
        'duracion'
    ];
}
