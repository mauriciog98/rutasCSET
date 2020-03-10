<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DuracionResultado extends Model
{
    protected $primaryKey = false;

    public $incrementing = false;

    protected $appends = ['resultados','resultados_count'];

    protected $hidden = ['competencia_id'];

    public function competencia()
    {
        return $this->belongsTo('App\Competencia');
    }

    public function getResultadosAttribute()
    {
        return Resultado::whereIn('id',$this->resultado)->get();
    }

    public function getResultadosCountAttribute()
    {
        return count($this->resultado);
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
