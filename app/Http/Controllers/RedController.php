<?php

namespace App\Http\Controllers;

use App\Http\Resources\RedesResource;
use App\Red;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RedController extends Controller
{
    const ITEM_PER_PAGE = 15;
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $redQuery = Red::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');


        if (!empty($keyword)) {
            $redQuery->where('nombre', 'LIKE', '%' . $keyword . '%');
        }

        return RedesResource::collection($redQuery->with('lider')->paginate($limit));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $red = Red::create($request->except('token'));
        return new RedesResource($red);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Red  $red
     * @return \Illuminate\Http\Response
     */
    public function show(Red $red)
    {
        return new RedesResource($red);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Red  $red
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Red $red)
    {
        $red->nombre = $request->get('nombre');
        $red->save();
        return new RedesResource($red);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Red $red
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Red $red)
    {
        if($red == null){
            return response()->json(['error' => 'Red no encontrada'], 404);
        }
        $red->delete();
        return response()->json(null, 204);
    }
}
