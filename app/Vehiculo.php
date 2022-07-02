<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    //protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id','placa','color','marca','tipo','conductor','propietario'
    ];
}
