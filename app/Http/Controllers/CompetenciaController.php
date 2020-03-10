<?php

namespace App\Http\Controllers;

use App\Competencia;
use App\Http\Resources\CompetenciaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CompetenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $competenciaQuery = Competencia::query();
        $keyword = Arr::get($searchParams, 'keyword', '');
        $programa = Arr::get($searchParams, 'programa', '');


        if (!empty($keyword)) {
            $competenciaQuery->where('nombre', 'LIKE', '%' . $keyword . '%');
        }

        return CompetenciaResource::collection($competenciaQuery->where('programa_id',$programa)->withCount('resultados')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $competencia = Competencia::create($request->except('token_api'));
        return new CompetenciaResource($competencia);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Competencia  $competencia
     * @return \Illuminate\Http\Response
     */
    public function show(Competencia $competencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Competencia  $competencia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competencia $competencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Competencia  $competencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competencia $competencia)
    {
        //
    }
}
