<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Indicators\Establecimiento;
use App\Indicators\Prestacion;
use App\Indicators\Rem;
use App\Indicators\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($year, $serie)
    {
        if(!Prestacion::exists($year)) abort(404);
        $prestaciones = Prestacion::year($year)->select('descripcion', 'Nserie')->where('serie', $serie)->orderBy('Nserie')->get();
        $prestaciones = $prestaciones->unique('Nserie');
        
        return view('indicators.rem.list', compact('prestaciones', 'year', 'serie'));
    }

    public function list($year)
    {
        if(!Prestacion::exists($year)) abort(404);
        $series = Prestacion::year($year)->select('serie')->distinct()->pluck('serie')->toArray();
        return view('indicators.rem.list_series', compact('year', 'series'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Indicators\Rem  $rem
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $year, $serie, $nserie)
    {
        if(!Prestacion::exists($year)) abort(404);
        $establecimientos = Establecimiento::year($year)->orderBy('comuna')->get();
        $prestacion = Prestacion::year($year)->where('serie', $serie)->where('Nserie', $nserie)->first();
        $establecimiento = $request->get('establecimiento');
        $periodo = $request->get('periodo');
        $secciones = null;
        if ($request->has('submit')) {
            $secciones = Seccion::year($year)->where('serie', $serie)->where('Nserie', $nserie)->orderby('name')->get();
            foreach($secciones as $seccion){
                $seccion->cods = explode(',', $seccion->cods);
                $seccion->cols = explode(',', $seccion->cols);
                $seccion->prestaciones = Prestacion::year($year)->with(['rems' => function($q) use ($establecimiento, $periodo){
                                                    $q->whereIn('IdEstablecimiento', $establecimiento)->whereIn('Mes', $periodo);
                                            }])
                                            ->whereIn('codigo_prestacion', $seccion->cods)->orderBy('id_prestacion')->get();
            }
        }
        
        return view('indicators.rem.show', compact('year', 'establecimientos', 'prestacion', 'establecimiento', 'periodo', 'secciones'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Indicators\Rem  $rem
     * @return \Illuminate\Http\Response
     */
    public function edit(Rem $rem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Indicators\Rem  $rem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rem $rem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Indicators\Rem  $rem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rem $rem)
    {
        //
    }
}
