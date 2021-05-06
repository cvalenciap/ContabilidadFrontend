<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoFacturado extends Model
{
    protected $table = 'documentos_facturados';

	protected $fillable = ['tipo_documento', 'codigo_documento', 'serie','numeracion','filename','fecha_venta','estado','tipo_pago','total','gravadas','igv'];
 
     public $timestamps = false;
}
