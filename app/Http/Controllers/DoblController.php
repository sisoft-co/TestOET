<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Dobl;
use App\DetalleOSalida;
use App\DetalleDobl;
use App\DetalleLiquidacion;
use App\User; 
use App\Notifications\NotifyAdmin;

//use App\Empresa;

class DoblController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $dobls = Dobl::join('empresas','dobls.nit_empresa','=','empresas.nit')
        ->join('users','dobls.id_user','=','users.id')
        ->select('dobls.dobl','dobls.fingreso','dobls.ntpallet','dobls.saldofin','dobls.sliqfin',
        'dobls.pnrecibido','dobls.observacion','dobls.estado','empresas.nombre','users.usuario')
        ->orderBy('dobls.created_at', 'desc')->get();
    
        $useridrol = \Auth::user()->idrol;

        return [ 'dobls' => $dobls, 'useridrol' => $useridrol ];
    }

    public function selectDobl(Request $request) // MAELCO -- Candidata a Eliminar
    {
        if (!$request->ajax()) return redirect('/');

        $criterio = $request->criterio;

        $dobls = Dobl::select('dobl','nit_empresa','producto as doblproducto','saldofin')
        ->where('nit_empresa', '=', $criterio)
        ->orderBy('dobl', 'asc')->get();
        return ['dobls' => $dobls];
    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tmp_ntpallet=0;

        try{

            DB::beginTransaction();
                $dobl = new Dobl();
                $dobl->dobl = $request->dobl;
                $dobl->nit_empresa = $request->nit_empresa;
                $dobl->id_user = \Auth::user()->id;
                $dobl->fingreso = $request->fingreso;
                $dobl->pnrecibido = $request->pnrecibido;
                $dobl->observacion = $request->observacion;
                $dobl->estado = 1;

                $detalles = $request->data;
                foreach($detalles as $ep=>$det)
                {
                    $detalle = new DetalleDobl();
                    $tmp_ntpallet=$tmp_ntpallet + $det['detntpallet'];
                }

                $dobl->ntpallet = $tmp_ntpallet;
                $dobl->saldofin = $tmp_ntpallet;
                $dobl->sliqfin = $tmp_ntpallet;
                //$dobl->fliqfin = $dobl->fingreso;
            $dobl->save();

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleDobl();
                    $detalle->dobl_dobls = $dobl->dobl;
                    $detalle->numcontenedor = $det['numcontenedor'];
                    $detalle->id_user = Auth::user()->id;
                    $detalle->fingresodobl = $dobl->fingreso;
                    $detalle->id_producto = $det['id_producto'];
                    $detalle->tipocontenedor = $det['tipocontenedor'];
                    $detalle->detntpallet = $det['detntpallet'];
                    $detalle->detsaldofin = $detalle->detntpallet;
                    $detalle->detsliqfin  = $detalle->detntpallet;
                    $detalle->vlrxpallet = $det['vlrxpallet'];
                    $detalle->lote = $det['lote'];
                    $detalle->diasnocobro = $det['diasnocobro'];
                    $detalle->detobservacion = $det['detobservacion'];
                    $detalle->puertoretiro = $det['puertoretiro'];
                    $detalle->puertoentrega = $det['puertoentrega'];

                    $detalle->lastfos  = $detalle->fingresodobl;
                    $detalle->lastfliq = $detalle->fingresodobl;
                $detalle->save();
            }
            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function cerrar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();
            
            $dobl = Dobl::findOrFail($request->dobl);
            $dobl->estado = 0;
            $dobl->save();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function validaabrir(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $dobl = Dobl::select('dobls.dobl','dobls.ntpallet','dobls.saldofin')
        ->where('dobls.dobl','=',$id)->take(1)->get();
        
        return ['dobl' => $dobl];
    }

    public function abrir(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        try{
            DB::beginTransaction();

            $dobl = Dobl::findOrFail($request->dobl);
            $dobl->estado = 1;
            $dobl->save();
            
            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function destroy(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $dobl = Dobl::findOrFail($request->id);
            $dobl->delete();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function obtenerCabecera(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $dobl = Dobl::join('empresas','dobls.nit_empresa','=','empresas.nit')
        ->join('users','dobls.id_user','=','users.id')
        ->select('dobls.dobl','dobls.fingreso','dobls.ntpallet','dobls.pnrecibido',
        'dobls.observacion','dobls.estado','empresas.nombre','users.usuario')
        ->where('dobls.dobl','=',$id)
        ->orderBy('dobls.dobl', 'desc')->take(1)->get();
        
        return ['dobl' => $dobl];
    }

    public function obtenerDetalles(Request $request){
        
        if (!$request->ajax()) return redirect('/');

        $id = $request->dobl_dobls;
        $detalles = DetalleDobl::join('users','det_dobls.id_user','=','users.id')
        ->join('productos','det_dobls.id_producto','=','productos.id')
        ->select('det_dobls.dobl_dobls','det_dobls.numcontenedor','det_dobls.id_producto','det_dobls.tipocontenedor','det_dobls.detntpallet',
        'det_dobls.detsaldofin','det_dobls.detsliqfin','det_dobls.vlrxpallet','det_dobls.lote','det_dobls.puertoretiro',
        'det_dobls.puertoentrega','det_dobls.detobservacion','det_dobls.diasnocobro','users.usuario',
        'productos.descripcion as desproducto')
        ->where('det_dobls.dobl_dobls','=',$id)->get();

        return ['detalles' => $detalles];
    }

    public function obtener_OSLIQ(Request $request){

        if (!$request->ajax()) return redirect('/');

        $id_dobl = $request->dobl_dobls;
        $id_numcontenedor = $request->numcontenedor;

        $contenedor_oss = DetalleOSalida::select('id_osalida')
        ->where('id_dobl','=',$id_dobl)->where('detnumcontenedor','=',$id_numcontenedor)->get();

        $contenedor_liqs = DetalleLiquidacion::select('id_liquidacion')
        ->where('id_dobl','=',$id_dobl)->where('numcontenedor','=',$id_numcontenedor)->get();

        if ($contenedor_oss == null) {
            return ['contenedor_oss' => [], 'contenedor_liqs' => []];
        }

        return['contenedor_oss' => $contenedor_oss, 'contenedor_liqs' => $contenedor_liqs];

    }

}
