<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompetenciaResource;
use App\Http\Resources\ProgramaResource;
use App\Laravue\JsonResponse;
use App\Programa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ProgramaController extends Controller
{
    const ITEM_PER_PAGE = 15;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $programaQuery = Programa::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');


        if (!empty($keyword)) {
            $programaQuery->where('nombre', 'LIKE', '%' . $keyword . '%');
        }

        return ProgramaResource::collection($programaQuery->with(['nivelFormacion','red'])->paginate($limit));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $programa = Programa::create($request->except('token_api'));
        return $programa;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Programa $programa
     * @return ProgramaResource
     */
    public function show(Programa $programa)
    {
        return new ProgramaResource($programa);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Programa  $programa
     * @return ProgramaResource
     */
    public function update(Request $request, Programa $programa)
    {
        if ($programa === null) {
            return response()->json(['error' => 'Programa no encontrado'], 404);
        }
        $codigo = $request->get('codigo');
        $version = $request->get('version');
        $found = Programa::where('codigo', $codigo)
                ->where('version', $version)
                ->first();
        if ($found && $found->id !== $programa->id) {
            return response()->json(['error' => 'Ya existe un programa con igual codigo y versión'], 403);
        }
        $programa->codigo=$request->get('codigo');
        $programa->version=$request->get('version');
        $programa->nombre=$request->get('nombre');
        $programa->nivel_formacion_id=$request->get('nivel_formacion_id');
        $programa->duracion=$request->get('duracion');
        $programa->duracion_lectiva=$request->get('duracion_lectiva');
        $programa->red_id=$request->get('red_id');
        $programa->estado=$request->get('estado');
        $programa->ruta=$request->get('ruta');
        $programa->save();
        return new ProgramaResource($programa);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Programa $programa
     * @return Response
     * @throws \Exception
     */
    public function destroy(Programa $programa)
    {
        if ($programa === null) {
            return response()->json(['error' => 'Programa no encontrado'], 404);
        }
        $programa->delete();
        return response()->json(null, 204);
    }

    /**
     * Devuelve un Array con las competencias tecnicas y transversales del programa.
     *
     * @param Programa $programa
     * @return JsonResponse
     */
    public function competencias(Programa $programa){
        return new JsonResponse([
            'tecnicas' => CompetenciaResource::collection($programa->competencias),
            'transversales' => CompetenciaResource::collection($programa->transversales),
        ]);
    }

    /**
     * Devuelve un Array con las competencias tecnicas y transversales del programa.
     *
     * @param Request $request
     * @param Programa $programa
     * @return ProgramaResource
     */
    public function updateCompetencias(Request $request, Programa $programa){
        if ($programa === null) {
            return response()->json(['error' => 'Programa no encontrado'], 404);
        }

        $competenciasTecnicas = $request->get('tecnicas',[]);
        $competenciasTransversalesIds = $request->get('transversales', []);
        $competenciasTecnicasNuevas = array_map(
            function($competencia) {
                if($competencia['id'] == null) {
                    $competencia['tipo'] = 1;
                    unset($competencia['id']);
                    return $competencia;
                }
            },
            $competenciasTecnicas
        );
        $programa->competencias()->createMany($competenciasTecnicasNuevas);
        $programa->transversales()->sync($competenciasTransversalesIds);;
        return new ProgramaResource($programa);
    }
}
