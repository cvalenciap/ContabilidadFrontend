<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presentacionventa extends Model
{
   protected $table = 'presentacionventa';

    protected $fillable = ['nuevocodigo','nuevonombre','identrada','idunidadmedida','stock','stock','precioventa'];
 public $timestamps = false;

  public function product()
    {

        return $this->hasOne('App\product');
    }


     public function entrada()
    {

        return $this->belongsTo('App\entrada', 'identrada', 'identrada');
    }
}
