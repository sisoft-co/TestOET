<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Liquidacion;
use App\DetalleLiquidacion;
use App\Dobl;
use App\DetalleDobl;
use App\OSalida;
use App\DetalleOSalida;
use App\User;
use DateTime;

class LiquidacionController extends Controller
{

    public function getLastFOS(Request $request){
        
        //if (!$request->ajax()) return redirect('/');

        $dobl = $request->dobl_dobls;
        $numcontenedor = $request->detnumcontenedor;

        //$lastfos = DetalleDobl::where(['dobl_dobls'=>$dobl, 'numcontenedor'=>$numcontenedor])->get();
        //$lastfos = DetalleDobl::where('dobl_dobls', '=', $dobl)->where('numcontenedor', '=', $numcontenedor)->first();
        //$lastfos = DB::table('det_dobls')->where([['dobl_dobls', '=', $dobl],['numcontenedor', '=', $numcontenedor]])->get();

        $lastfos = DetalleDobl::where(['dobl_dobls'=>$dobl, 'numcontenedor'=>$numcontenedor])->first();
        //->update(['detsliqfin' => $detalle->saldofin]);
        
        return ['det_lastfos' => $lastfos];
    }

    public function consultaCliente(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $nit_empresa = \Auth::user()->nit_empresa;
        $detos = Liquidacion::join('users','liquidaciones.id_user','=','users.id')
        ->join('empresas','liquidaciones.nit_empresa','=','empresas.nit')
        ->select('liquidaciones.id','liquidaciones.fechaliqui','liquidaciones.fechacorte',
        'liquidaciones.observacion','liquidaciones.vlrtotalliq','liquidaciones.estado','users.usuario','empresas.nombre as nombreEmpresa')
        ->where('liquidaciones.nit_empresa', '=', $nit_empresa)
        ->orderBy('liquidaciones.id', 'desc')->get();

        return ['detos' => $detos];

    }

    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $useridrol = \Auth::user()->idrol;

        $detos = Liquidacion::join('users','liquidaciones.id_user','=','users.id')
        ->join('empresas','liquidaciones.nit_empresa','=','empresas.nit')
        ->select('liquidaciones.id','liquidaciones.fechaliqui','liquidaciones.fechacorte',
        'liquidaciones.observacion','liquidaciones.vlrtotalliq','liquidaciones.estado','users.usuario','empresas.nombre')
        //->where('liquidaciones.estado', '=', $selectedEstado)
        ->orderBy('liquidaciones.id', 'desc')->get();
        //->orderBy('empresas.nombre')->orderBy('liquidaciones.id', 'desc')->get();

        //$statement = DB::select("show table status like 'liquidaciones'");
        //$lastidliq = $statement[0]->Auto_increment;

        return ['detos' => $detos, 'useridrol' => $useridrol];
    }

    public function pdf(Request $request, $id){
        $usuario = \Auth::user()->usuario;

        $liquidacion = Liquidacion::join('empresas','liquidaciones.nit_empresa','=','empresas.nit')
        ->join('users','liquidaciones.id_user','=','users.id')
        ->select('liquidaciones.id','empresas.nombre','liquidaciones.fechaliqui','liquidaciones.fechacorte',
                'liquidaciones.observacion','liquidaciones.vlrtotalliq','users.usuario')
        ->where('liquidaciones.id','=',$id)->get();

        $detalles = DetalleLiquidacion::where('det_liquidaciones.id_liquidacion','=',$id)->get();

        $datetime = new DateTime();
        $timezone = new \DateTimeZone('America/Bogota');
        $datetime->setTimezone($timezone);
        $factual = $datetime->format('Y-m-d / H:i');

        $pdf = \PDF::loadView('pdf.liquidacion',['liquidacion'=>$liquidacion, 'detalles'=>$detalles, 'factual'=>$factual, 'usuario'=>$usuario]);
        return $pdf->download('Liquidacion-'.$id.'.pdf');
    }

    public function searchDOBL(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $nit_empresa = $request->nit_empresa;
        $fecha_corte = $request->fechaCorte;

        $hay_dobls[] = Dobl::where('dobls.nit_empresa', $nit_empresa)->where('sliqfin','>','saldofin')->get(); // $hay_dobls->Object; $hay_dobls[]->Array
        if (!empty($hay_dobls)) {               //if (!blank($hay_dobls)) {             //if ($hay_dobls) {

            $det_dobls = DetalleOSalida::join('dobls','det_osalidas.id_dobl','=','dobls.dobl')
            ->select('det_osalidas.id_dobl','det_osalidas.fingresodobl','det_osalidas.id_liquidacion', 
                    'dobls.ntpallet','dobls.pnrecibido','dobls.observacion')
            ->where('det_osalidas.detnit_empresa', '=', $nit_empresa)
            ->whereNull('det_osalidas.id_liquidacion')
            ->orderBy('det_osalidas.fingresodobl', 'desc')->distinct()->get();

            foreach($det_dobls as $ep=>$detos){

                $det_detdobls[] = DetalleDobl::select('lastfos','dobl_dobls','numcontenedor')->where('dobl_dobls','=',$detos['id_dobl'])->get();
                //$det_detdobls[] = $tmp_detdobls;

                $det_osalidas[] = DetalleOSalida::join('productos','det_osalidas.id_producto','=','productos.id')
                ->select('det_osalidas.id_dobl','det_osalidas.fingresodobl','det_osalidas.id_osalida',
                'det_osalidas.fsalida','det_osalidas.detnumcontenedor','det_osalidas.saldoinicio','det_osalidas.cantretirada',
                'det_osalidas.saldofin','det_osalidas.diasnocobro','det_osalidas.diastotalliq','det_osalidas.vlrxpallet','det_osalidas.vlrtotalliq',
                'productos.descripcion','det_osalidas.empaque','det_osalidas.pbruto','det_osalidas.pneto','det_osalidas.detobservacion')

                ->where('det_osalidas.id_dobl', '=', $detos['id_dobl'])
                ->where('det_osalidas.id_liquidacion','=',null)
                ->orderBy('det_osalidas.id_osalida', 'asc')->orderBy('det_osalidas.detnumcontenedor', 'asc')->get();  //->toArray();
            }

            //$statement = DB::select("show table status like 'liquidaciones'");
            //$lastidliq = $statement[0]->Auto_increment;
            $det_osalidas[] = [0];  // [0] envía todo lo que hay en la variable, pero si esta queda indefinida porque la consulta no halla
                                    // registros entonces llena la variable con ceros.... así ya no pasa como indefinida.
        }
        else{

            //  return ['det_dobls' => [], 'lastidliq' => [], 'det_osalidas' => [], 'det_detdobls' => []];
            $det_detdobls[] = [0]; $det_dobls[] = [0]; $det_osalidas[] = [0]; 
        }
        $det_detdobls[] = [0]; //$det_dobls[]=[0]; $det_osalidas[]=[0]; 

        $statement = DB::select("show table status like 'liquidaciones'");
        $lastidliq = $statement[0]->Auto_increment;

        $detdobls_sinos[] = DetalleDobl::where('detnit_empresa', $nit_empresa)->where('fingresodobl', '<', $fecha_corte)
                            ->whereRaw('detsaldofin = detsliqfin')->get(); // $hay_dobls->Object; $hay_dobls[]->Array

        

        return [ 'hay_dobls' => $hay_dobls, 'det_dobls' => $det_dobls, 'lastidliq' => $lastidliq, 
                'det_osalidas' => $det_osalidas, 'det_detdobls' => $det_detdobls, 'detdobls_sinos' => $detdobls_sinos ];
    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id_liquidacion;
        $detalles = DetalleLiquidacion::where('det_liquidaciones.id_liquidacion','=',$id)->get();

        return ['detalles' => $detalles];
    }
        
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $liquidacion = new Liquidacion();
                //$liquidacion->id = $request->id;
                $liquidacion->nit_empresa = $request->nit_empresa;
                $liquidacion->id_user = \Auth::user()->id;
                $liquidacion->fechaliqui = $request->fechaliqui;
                $liquidacion->fechacorte = $request->fechacorte;
                $liquidacion->vlrtotalliq = $request->vlrtotalliq;
                $liquidacion->observacion = $request->observacion;
                $liquidacion->estado = 1;

            $liquidacion->save();

            $detalles = (json_decode($request->data));   //data;  // Array de Detalles de Liquidaciones

            foreach($detalles as $ep => $det)
            {
                $detalle = new DetalleLiquidacion();
                    $detalle->id_liquidacion = $liquidacion->id;
                    $detalle->id_dobl = $det->id_dobl;
                    $detalle->id_osalida = $det->id_osalida;
                    $detalle->numcontenedor = $det->detnumcontenedor;
                    $detalle->id_user = Auth::user()->id;
                    $detalle->fechaingdobl = $det->fingresodobl;
                    $detalle->fechaosalida = $det->fsalida;
                    $detalle->saldoinicial = $det->saldoinicio;
                    $detalle->cantretirada = $det->cantretirada;
                    $detalle->diasnocobro = $det->diasnocobro;
                    $detalle->saldofinal = $det->saldofin;
                    $detalle->totaldias = $det->diastotalliq;
                    $detalle->vlrxpallet = $det->vlrxpallet;
                    $detalle->vlrtotal = $det->vlrtotalliq;
                    //$detalle->estado = $det['estado'];
                $detalle->save();

                $dobls = Dobl::findOrFail( $detalle->id_dobl );
                    $dobls->sliqfin -= $detalle->cantretirada;
                $dobls->save();

                $det_dobl = DetalleDobl::where(['dobl_dobls'=>$detalle->id_dobl, 'numcontenedor'=>$detalle->numcontenedor])
                    ->update(['detsliqfin' => $detalle->saldofinal]);

                $os = OSalida::where('id', $detalle->id_osalida)->first();
                    $os->id_liquidacion = $detalle->id_liquidacion;
                    $os->estado = 0;
                $os->save();

                $det_os = DetalleOSalida::where(['id_osalida'=>$detalle->id_osalida, 'id_dobl'=>$detalle->id_dobl, 'detnumcontenedor'=>$detalle->numcontenedor])
                    ->update(['vlrtotalliq'=>$detalle->vlrtotal, 'diastotalliq'=>$detalle->totaldias, 'ffincorte'=>$liquidacion->fechacorte, 
                    'id_liquidacion'=>$detalle->id_liquidacion]);

            }
            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function destroy(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $liquidacion = Liquidacion::find($request->id);
        try{
            DB::beginTransaction();

            $det_liq = DetalleLiquidacion::where('id_liquidacion', $request->id)->get();
            foreach($det_liq as $ep => $detalle)
            {
                // Modifica el respectivo encabezado DO/BL restando la Cantidad Retirada de DetalleLiquidación
                $dobls = Dobl::findOrFail( $detalle->id_dobl );
                    //$dobls->sliqfin -= $detalle->cantretirada;
                    $tmp_sliqfin = $dobls->sliqfin - $detalle->cantretirada;
                    if ($tmp_sliqfin < 0) {
                        $dobls->sliqfin = 0;
                    } else {
                        $dobls->sliqfin += $detalle->cantretirada;
                    };
                $dobls->save();

                // Modifica el Detalle DO/BL-Contenedor re-estableciendo la cantidad Retirada (sumando)
                $det_dobl = DetalleDobl::where(['dobl_dobls'=>$detalle->id_dobl, 'numcontenedor'=>$detalle->numcontenedor])
                            ->increment('detsliqfin', $detalle->cantretirada);

                // Modifica la O.S. estableciendo el Número de Liquidación a NULL
                $os = OSalida::where('id', $detalle->id_osalida)->first();
                $os->id_liquidacion = null;
                $os->estado = 1; //0;
                $os->save();

                $det_os = DetalleOSalida::where(['id_osalida'=>$detalle->id_osalida, 'id_dobl'=>$detalle->id_dobl, 'detnumcontenedor'=>$detalle->numcontenedor])
                            ->update(['vlrtotalliq' => null, 'diastotalliq' => null, 'ffincorte' => null, 'id_liquidacion' => null]);

            }
            $liquidacion->delete();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function abrir(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $liquidacion = Liquidacion::findOrFail($request->id);
            $liquidacion->estado = 1;
            $liquidacion->save();

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

            $liquidacion = Liquidacion::findOrFail($request->id);
            $liquidacion->estado = 0;
            $liquidacion->save();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }    
}