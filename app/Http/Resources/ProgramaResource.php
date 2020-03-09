<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramaResource extends JsonResource
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
            'codigo' => $this->codigo,
            'version' => $this->version,
            'nombre' => $this->nombre,
            'nivel_formacion' => new NivelFormacionResource($this->whenLoaded('nivelFormacion')),
            'duracion' => $this->duracion,
            'duracion_lectiva' => $this->duracion_lectiva,
            'red' => new RedesResource($this->whenLoaded('red')),
            'estado' => $this->estado,
            'ruta' => $this->ruta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
