<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompetenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'tipo' => $this->tipo,
            'resultados' => ResultadoResource::collection($this->whenLoaded('resultados')),
            'resultado' => $this->resultado,
            'programa' => new ProgramaResource($this->whenLoaded('programa')),
            'duracionResultados' => DuracionResultadoResource::collection($this->whenLoaded('duracionResultados')),
            'programacion_resultados' => ProgramacionResultadoResource::collection($this->programacion_resultados),
            'resultados_count' => $this->resultados_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
