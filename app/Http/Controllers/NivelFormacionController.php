<?php

namespace App\Http\Controllers;

use App\Http\Resources\NivelFormacionResource;
use App\NivelFormacion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class NivelFormacionController extends Controller
{
    const ITEM_PER_PAGE = 15;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $nivelFormacionQuery = NivelFormacion::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');


        if (!empty($keyword)) {
            $nivelFormacionQuery->where('nombre', 'LIKE', '%' . $keyword . '%');
        }

        return NivelFormacionResource::collection($nivelFormacionQuery->paginate($limit));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $programa = NivelFormacion::create($request->except('token_api'));
        return $programa;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NivelFormacion  $nivelFormacion
     * @return \Illuminate\Http\Response
     */
    public function show(NivelFormacion $nivelFormacion)
    {
        return new NivelFormacionResource($nivelFormacion);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NivelFormacion  $nivelFormacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NivelFormacion $nivelFormacion)
    {
        if ($nivelFormacion === null) {
            return response()->json(['error' => 'Nivel de formación no encontrado'], 404);
        }
        $nivelFormacion->nombre=$request->get('nombre');
        $nivelFormacion->save();
        return new NivelFormacionResource($nivelFormacion);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NivelFormacion  $nivelFormacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(NivelFormacion $nivelFormacion)
    {
        if ($nivelFormacion === null) {
            return response()->json(['error' => 'Nivel de formación no encontrado'], 404);
        }
        $nivelFormacion->delete();
        return response()->json(null, 204);
    }
}
