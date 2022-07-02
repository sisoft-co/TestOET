<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Rol;

class RolController extends Controller
{
    public function index(Request $request)
    {
        // Se elimina lo que había porque no es necesario..... 
    }
    public function selectRol(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $roles = Rol::where('condicion', '=', '1')
        ->select('id','nombre')
        ->orderBy('id', 'asc')->get();

        return ['roles' => $roles];
    } 
}
