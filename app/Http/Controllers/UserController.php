<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Persona;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
            $userid = \Auth::user()->id;

            $personas = DB::select('select * from personas');

        return [ 'personas' => $personas, 'userid' => $userid ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $persona = new Persona();
            $persona->num_documento = $request->num_documento;
            $persona->primernombre  = $request->primernombre;
            $persona->segundonombre = $request->segundonombre;
            $persona->apellidos     = $request->apellidos;
            $persona->direccion     = $request->direccion;
            $persona->telefono      = $request->telefono;
            $persona->ciudad        = $request->ciudad;
            $persona->save();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            // $user = User::findOrFail($request->id);
            $persona = Persona::findOrFail($request->id);
            $persona->num_documento = $request->num_documento;
            $persona->primernombre  = $request->primernombre;
            $persona->segundonombre = $request->segundonombre;
            $persona->apellidos     = $request->apellidos;
            $persona->direccion     = $request->direccion;
            $persona->telefono      = $request->telefono;
            $persona->ciudad        = $request->ciudad;
            $persona->save();
            
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function updateAuthUserPassword(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $this->validate($request, [
            'current' => 'required',
            'password' => 'required|confirmed|min:5',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::find(Auth::id());
        if (!Hash::check($request->current, $user->password)) {
            return response()->json(['errors' => ['current'=> ['Contraseña Digitada es Incorrecta.  Inténte de Nuevo']]], 422);
        }

        try{
            DB::beginTransaction();

            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        } finally {
            return $user;
        }
    }

    public function destroy(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            // $user = User::find($request->id);
            // $user->delete();

            $persona = Persona::find($request->id);
            $persona->delete();

            DB::commit();
        } catch (Exeption $e){
            DB::rollBack();
        }
    }
}
