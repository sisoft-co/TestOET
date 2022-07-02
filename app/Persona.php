<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = ['id','num_documento','primernombre','segundonombre','apellidos','direccion','telefono','ciudad'];

    public function user()
    {
        return $this->hasOne('App\User');
    }


}
