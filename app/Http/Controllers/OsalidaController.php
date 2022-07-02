<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Dobl;
use App\DetalleDobl;
use App\OSalida;
use App\DetalleOSalida;
use App\User; 
use DateTime;

//use App\Empresa;

class OsalidaController extends Controller
{
    public function consultaCliente(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $nit_empresa = \Auth::user()->nit_empresa;
        $detos = OSalida::join('users','osalidas.id_user','=','users.id')
        ->join('empresas','osalidas.nit_empresa','=','empresas.nit')
        ->select('osalidas.id','empresas.nombre','osalidas.fsalida','osalidas.vehplaca','osalidas.nomchofer',
                'osalidas.ocarcliente','osalidas.ciudestino','osalidas.observacion','osalidas.id_liquidacion',
                'osalidas.estado','empresas.nombre as nombreEmpresa')
        ->where('osalidas.nit_empresa', '=', $nit_empresa)
        ->orderBy('osalidas.id', 'desc')->get();

        return ['detos' => $detos];

    }

    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $useridrol = \Auth::user()->idrol;

        $detos = OSalida::join('users','osalidas.id_user','=','users.id')
        ->join('empresas','osalidas.nit_empresa','=','empresas.nit')
        ->select('osalidas.id','empresas.nombre','osalidas.fsalida','osalidas.vehplaca','osalidas.nomchofer',
                'osalidas.ocarcliente','osalidas.ciudestino','osalidas.observacion','osalidas.id_liquidacion',
                'osalidas.estado')
        ->orderBy('osalidas.id', 'desc')->get();
        $statement = DB::select("show table status like 'osalidas'");
        $lastidos = $statement[0]->Auto_increment;        

        return ['detos' => $detos, 'useridrol' => $useridrol, 'lastidos' => $lastidos];    //, 'lastidos' => $lastidos
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $useridrol = \Auth::user()->idrol;

        $osalidas = Osalida::join('empresas','osalidas.nit_empresa','=','empresas.nit')
        ->select('osalidas.id','osalidas.fsalida','osalidas.ciudestino','osalidas.ocarcliente','osalidas.cchofer',
                 'osalidas.nomchofer','osalidas.vehplaca','osalidas.observacion','osalidas.picchofer',
                 'osalidas.picvehiculo','osalidas.tcantretirada','osalidas.estado','empresas.nombre')
        ->where('osalidas.id','=',$id)->get();

        return ['osalidas' => $osalidas, 'useridrol' => $useridrol];
    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id_osalida;
        $detalles = DetalleOSalida::join('productos','det_osalidas.id_producto','=','productos.id')
        ->select('det_osalidas.*','productos.descripcion')
        ->where('det_osalidas.id_osalida','=',$id)->get();

        return ['detalles' => $detalles];
    }


    public function obtenerLiquidacion(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id_osalida = $request->id_osalida;
        $liquidaciones = DetalleOSalida::select('id_liquidacion')
            ->where('id_osalida','=',$id_osalida)->get();

        if (!$liquidaciones) {
            return ['liquidaciones' => []];
        }
        return ['liquidaciones' => $liquidaciones];
    }



    public function obtenerDetalles_OLD(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleOSalida::join('productos','det_osalidas.id_producto','=','productos.id')
        ->select('det_osalidas.id_dobl','det_osalidas.detnumcontenedor','det_osalidas.id_producto','productos.descripcion',
                'det_osalidas.saldoinicio','det_osalidas.cantretirada','det_osalidas.saldofin','det_osalidas.empaque',
                'det_osalidas.pbruto','det_osalidas.pneto','det_osalidas.detobservacion','det_osalidas.id_liquidacion')
        ->where('det_osalidas.id_osalida','=',$id)->get();

        foreach($detalles as $ep=>$det){
           if($det['id_liquidacion']>0){$detosliquidado = 1;}
           else {$detosliquidado = 0;}   
        }

        return ['detalles' => $detalles, 'detosliquidado' => $detosliquidado];
    }

    public function selectDoblsOS(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $dobls = Dobl::select('dobl','nit_empresa','saldofin')
        ->where('nit_empresa', '=', $id)->where('saldofin', '>','0')
        ->orderBy('dobl', 'asc')->get();

        return ['dobls' => $dobls];
    }

    public function selectContenedores(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $contenedores = DetalleDobl::join('productos','det_dobls.id_producto','=','productos.id')
        ->select('numcontenedor','detntpallet','detsaldofin','id_producto','descripcion',
                 'fingresodobl','diasnocobro','vlrxpallet')
        ->where('dobl_dobls', '=', $id)
        ->orderBy('numcontenedor', 'asc')->get();

        return ['contenedores' => $contenedores];
    }

    public function selectOSalida(Request $request) // MAELCO -- Candidata a Eliminar
    {
        if (!$request->ajax()) return redirect('/');
        
        $osalidas = Osalida::select('osalida','id')->orderBy('osalida', 'asc')->get();
        return ['osalidas' => $osalidas];
    }

    public function pdf(Request $request, $id){
        $usuario = \Auth::user()->usuario;

        $osalida = Osalida::join('empresas','osalidas.nit_empresa','=','empresas.nit')
        ->join('users','osalidas.id_user','=','users.id')
        ->select('osalidas.id','osalidas.nit_empresa','osalidas.fsalida','osalidas.ciudestino','osalidas.ocarcliente',
                'osalidas.picchofer','osalidas.picvehiculo','osalidas.cchofer','osalidas.nomchofer','osalidas.vehplaca',
                'osalidas.observacion','empresas.nombre','empresas.direccion','empresas.telefono1','empresas.telefono2',
                'empresas.contacto','empresas.email','users.usuario')
        ->where('osalidas.id','=',$id)->get();

        $detos = DetalleOSalida::join('empresas','det_osalidas.detnit_empresa','=','empresas.nit')
        ->join('productos','det_osalidas.id_producto','=','productos.id')
        ->select('det_osalidas.id_dobl','det_osalidas.detnumcontenedor','det_osalidas.detnit_empresa','det_osalidas.saldoinicio',
                'det_osalidas.cantretirada','det_osalidas.saldofin','det_osalidas.empaque','det_osalidas.pbruto','det_osalidas.pneto',
                'det_osalidas.detobservacion','productos.descripcion')
        ->where('det_osalidas.id_osalida','=',$id)->get();

        $datetime = new DateTime();
        $timezone = new \DateTimeZone('America/Bogota');
        $datetime->setTimezone($timezone);
        $factual = $datetime->format('Y-m-d / H:i');

        //return view('pdf.ordensalida',['osalida'=>$osalida, 'detos'=>$detos]);
        $pdf = \PDF::loadView('pdf.ordensalida',['osalida'=>$osalida, 'detos'=>$detos, 'factual'=>$factual, 'usuario'=>$usuario]);
        return $pdf->download('O.Salida-'.$id.'.pdf');
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        if($request->hasFile('picchofer')){
            $file = $request->file('picchofer');
            $picchofer = time().$file->getClientOriginalName();
            $destinationPath = storage_path('/app/public/imagenes/', $picchofer);
            //$destinationPath = public_path('storage/imagenes', $picchofer);
            $file->move($destinationPath, $picchofer);
        } 
        if($request->hasFile('picvehiculo')){
            $file = $request->file('picvehiculo');
            $picvehiculo = time().$file->getClientOriginalName();
            $destinationPath = storage_path('/app/public/imagenes/', $picvehiculo);
            //$destinationPath = public_path('storage/imagenes', $picvehiculo);
            $file->move($destinationPath, $picvehiculo);
        }

        try{
            DB::beginTransaction();

                $osalida = new OSalida();
                    //$osalida->id = $request->id;
                    $osalida->nit_empresa = $request->nit_empresa;
                    $osalida->id_user = \Auth::user()->id;
                    $osalida->fsalida = $request->fsalida;
                    $osalida->ciudestino = $request->ciudestino;
                    $osalida->ocarcliente = $request->ocarcliente;
                    $osalida->cchofer = $request->cchofer;
                    $osalida->nomchofer = $request->nomchofer;
                    $osalida->vehplaca = $request->vehplaca;
                    $osalida->tcantretirada = $request->tcantretirada;
                    $osalida->observacion = $request->observacion;
                    $osalida->estado = 1;

                    if (!empty($picchofer)){ 
                        $osalida->picchofer = $picchofer;
                    }
                    if(!empty($picvehiculo)){ 
                        $osalida->picvehiculo = $picvehiculo;
                    }
                $osalida->save();

                $detalles = (json_decode($request->data));   //data;  // Array de Detalles de OSalidas
                foreach($detalles as $ep=>$det)
                {
                    $detalle = new DetalleOSalida();
                        $detalle->id_osalida = $osalida->id;
                        $detalle->id_dobl = $det->id_dobl;  //['id_dobl'];
                        $detalle->detnumcontenedor = $det->detnumcontenedor;  //['id_dobl'];
                        $detalle->id_user = Auth::user()->id;
                        $detalle->detnit_empresa = $osalida->nit_empresa;   
                        $detalle->fsalida = $osalida->fsalida;           
                        $detalle->id_producto = $det->id_producto; //['producto'];                 // PROVIENE DE DET_DOBLS.PRODUCTO
                        $detalle->saldoinicio = $det->saldoinicio; //['saldoinicio'];           // PROVIENE DE DET_DOBLS.SALDOFIN
                        $detalle->cantretirada = $det->cantretirada;  //['cantretirada'];
                        $detalle->saldofin = $det->saldoinicio - $det->cantretirada;  //['saldoinicio'] - $det['cantretirada'];    // RESTA DE SALDOINICIO - CANTRETIRADA
                        $detalle->vlrxpallet = $det->vlrxpallet;
                        $detalle->empaque = $det->empaque;  //['empaque'];
                        $detalle->pbruto = $det->pbruto;  //['pbruto'];
                        $detalle->pneto = $det->pneto;  //['pneto'];
                        $detalle->fingresodobl = $det->fingresodobl;
                        $detalle->diasnocobro = $det->diasnocobro;
                        $detalle->detobservacion = $det->detobservacion;  //['pneto'];
                        //$detalle->id_liquidacion = $det->id_liquidacion;  //['id_liquidacion'];
                        //$detalle->estado = $det['estado'];
                    $detalle->save();

                    $det_dobl = DetalleDobl::where(['dobl_dobls'=>$detalle->id_dobl, 'numcontenedor'=>$detalle->detnumcontenedor])
                                ->update(['detsaldofin' => $detalle->saldofin]);

                    $dobls = Dobl::findOrFail($detalle->id_dobl);
                        $dobls->saldofin -= $detalle->cantretirada;
                        $dobls->estado = 0;
                    $dobls->save();
                }

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }

    public function destroy(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $osalida = OSalida::find($request->id);  //public_path('storage/imagenes', $picchofer);
        $deleteFileChofer = storage_path().'/app/public/imagenes/'.$osalida->picchofer;
        $deleteFileVehi = storage_path().'/app/public/imagenes/'.$osalida->picvehiculo;        

        try{
            $bug = 0;
            DB::beginTransaction();
                $det_os = DetalleOsalida::where('id_osalida', $request->id)->get();

                //return ['arraydetos' => $det_os];

                foreach ($det_os as $key=>$detalle) {
                    $dobls = Dobl::findOrFail($detalle->id_dobl);
                    $dobls->saldofin = $dobls->saldofin + $detalle->cantretirada;
                    $dobls->save();

                    $det_dobl = DetalleDobl::where(['dobl_dobls'=>$detalle->id_dobl, 'numcontenedor'=>$detalle->detnumcontenedor])
                                ->increment('detsaldofin', $detalle->cantretirada);
                }
                $osalida->delete();

            DB::commit();
        } catch (Exception $e){
            $bug = $e->errorInfo[1];
            DB::rollBack();
        }
        if($bug==0){
            if(@getimagesize($deleteFileChofer)){
                unlink($deleteFileChofer);
            }
            if(@getimagesize($deleteFileVehi)){
                unlink($deleteFileVehi);
            }
        }
    }

    public function abrir(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $osalida = OSalida::findOrFail($request->id);
            $osalida->estado = 1;
            $osalida->save();
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

            $osalida = OSalida::findOrFail($request->id);
            $osalida->estado = 0;
            $osalida->save();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }
}
