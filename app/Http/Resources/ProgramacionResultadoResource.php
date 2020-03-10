<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramacionResultadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'resultados' => ResultadoResource::collection($this->resultados),
            'resultados_count' => $this->resultados_count,
            'duracion' => $this->duracion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
