<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Empresa;

class DashboardController extends Controller

{
    public function __invoke(Request $request)
    {
        $anio=date('Y');
        $dobls=DB::table('dobls as i')
        ->select(DB::raw('MONTH(i.created_at) as mes'),
        DB::raw('YEAR(i.created_at) as anio'),
        DB::raw('SUM(i.ntpallet) as total'))
        ->whereYear('i.created_at',$anio)
        ->groupBy(DB::raw('MONTH(i.created_at)'),DB::raw('YEAR(i.created_at)'))
        ->get();

        $liquidaciones=DB::table('liquidaciones as v')
        ->select(DB::raw('MONTH(v.created_at) as mes'),
        DB::raw('YEAR(v.created_at) as anio'),
        DB::raw('SUM(v.vlrtotalliq) as total'))
        ->whereYear('v.created_at',$anio)
        ->groupBy(DB::raw('MONTH(v.created_at)'),DB::raw('YEAR(v.created_at)'))
        ->get();

        $nit_empresa = \Auth::user()->nit_empresa;
        if (!$nit_empresa) {
            return ['dobls'=>$dobls,'liquidaciones'=>$liquidaciones,'anio'=>$anio];
        } else {
            $empresa = Empresa::select('nombre')->findOrFail($nit_empresa);
            return ['dobls'=>$dobls,'liquidaciones'=>$liquidaciones,'anio'=>$anio, 'empresa'=>$empresa];
        }
    }
}
