<?php

namespace App\Http\Controllers;

use App\DuracionResultado;
use App\Http\Resources\DuracionResultadoResource;
use Illuminate\Http\Request;

class DuracionResultadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return DuracionResultadoResource
     */
    public function store(Request $request)
    {
        $duracion = DuracionResultado::findOrNew($request->get('duracion_id'));
        $duracion->resultado = $request->get('resultados');
        $duracion->duracion = $request->get('duracion');
        $duracion->competencia_id = $request->get('competencia.id');
        $duracion->save();
        return new DuracionResultadoResource($duracion);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DuracionResultado  $duracionResultado
     * @return \Illuminate\Http\Response
     */
    public function show(DuracionResultado $duracionResultado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DuracionResultado  $duracionResultado
     * @return \Illuminate\Http\Response
     */
    public function edit(DuracionResultado $duracionResultado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DuracionResultado  $duracionResultado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DuracionResultado $duracionResultado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DuracionResultado  $duracionResultado
     * @return \Illuminate\Http\Response
     */
    public function destroy(DuracionResultado $duracionResultado)
    {
        //
    }
}
