<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ordencompra extends Model
{
   protected $table = 'ordencompra';

	protected $fillable = ['formapago', 'idusuario', 'idcliente','subtotal','pago','total','estado','estadoorden','IdCajero','updated_at','created_at','tipoventa','codigoTotales','tipodocumento','correlativo'];
 public $timestamps = true;

	// Relation with User
	public function user()
	{
	    return $this->belongsTo('App\User','idusuario','id');
	}
		public function cliente()
	{
	    return $this->belongsTo('App\Cliente','idcliente','idcliente');
	}
	
	

	public function ordenlista()
	{
	    return $this->hasMany('App\Ordenlista');
	}
}
