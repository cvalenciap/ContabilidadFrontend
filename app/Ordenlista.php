<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ordenlista extends Model
{
    protected $table = 'ordenlista';

	protected $fillable = ['cantidad', 'precio', 'idproducto','idordencompra','presentacion','identrada','idpresentacionventa'];
 public $timestamps = false;
	// Relation with User
	public function producto()
	{
	    return $this->belongsTo('App\product');
	}
		public function ordencompra()
	{
	    return $this->belongsTo('App\Ordencompra');
	}
}
