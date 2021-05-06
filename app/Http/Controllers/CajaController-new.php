<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Presentacionventa;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Ws\Services\SunatEndpoints;
use App\Util;
use Carbon\Carbon;
use NumeroALetras\NumeroALetras;
use App\DocumentoFacturado;

use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use mikehaertl\wkhtmlto\Pdf;


class CajaController extends Controller
{
      

 public function __construct()
    {
         
        
        if(!\Session::has('cart')) \Session::put('cart', array());
    }

   

public  function facturador(Request $request){

$carrito=$request->cart;

$fac=$request->form;

$items=[];


$tip=array_get($fac, 'fradio');


if($tip=='factura'){


   // return response()->json(array_get($fac, 'serie'));
   $util = Util::getInstance();
   $client = new Client();
   $client ->setTipoDoc(array_get($fac, 'tipo_documento'))
   ->setNumDoc(array_get($fac, 'documento_identidad'))
   ->setRznSocial(array_get($fac, 'nombre_cliente'))
   ->setAddress((new Address())
   ->setDireccion(array_get($fac, 'direccion')));

   $invoice = new Invoice();
   $invoice
       ->setUblVersion('2.1')
       ->setFecVencimiento(new \DateTime())
       ->setTipoOperacion('0101')
       ->setTipoDoc('01')
       ->setSerie('F001')
       ->setCorrelativo(array_get($fac, 'numero_doc'))
       ->setFechaEmision(new \DateTime())
       ->setTipoMoneda('PEN')
       ->setClient($client)
       ->setMtoOperGravadas(array_get($fac, 'cartSubTotal'))
       ->setMtoOperExoneradas(0)
       ->setMtoIGV(array_get($fac, 'cartIgv'))
       ->setTotalImpuestos(array_get($fac, 'cartIgv'))
       ->setValorVenta(array_get($fac, 'cartSubTotal'))
       ->setMtoImpVenta(array_get($fac, 'cartTotal'))
       ->setCompany($util->getCompany());
   
for ($i = 0; $i < count($carrito); $i++) {

     $item = new SaleDetail();
      $item->setCodProducto(array_get($carrito[$i], 'id'))
            ->setUnidad(array_get($carrito[$i], 'unidad'))
            ->setDescripcion(array_get($carrito[$i], 'producto'))
            ->setCantidad(array_get($carrito[$i], 'quantity'))
            ->setMtoValorUnitario(array_get($carrito[$i], 'precio'))
            ->setMtoValorVenta(array_get($carrito[$i], 'importe'))
            ->setMtoBaseIgv(array_get($carrito[$i], 'subtotal'))
            ->setPorcentajeIgv(18)
            ->setIgv(array_get($carrito[$i], 'igv'))
            ->setTipAfeIgv('10')
            ->setTotalImpuestos(array_get($fac, 'cartIgv'))
            ->setMtoPrecioUnitario(array_get($carrito[$i], 'precio'));
      $items[]=$item;
}


$Total=NumeroALetras::convertir(array_get($fac, 'cartTotal'));
   
   $invoice->setDetails($items)
       ->setLegends([
           (new Legend())
               ->setCode('1000')
               ->setValue($Total
               )
       ]);

     


   //$see = $util->getSee(SunatEndpoints::FE_PRODUCCION);
   $see = $util->getSee(SunatEndpoints::FE_BETA);
   $res = $see->send($invoice);
   $util->writeXml($invoice, $see->getFactory()->getLastXml());


   $filename = $invoice->getName();
   $pdf = $util->getPdf($invoice);
   $util->showPdf($pdf, $filename.'.pdf');
   $nc=str_pad(array_get($fac, 'numero_doc'), 6, "0", STR_PAD_LEFT);

   if ($result->isSuccess()) {

    $filename = $invoice->getName();
    $pdf = $util->getPdf($invoice);
    $cdr = $result->getCdrResponse();
    $util->writeCdr($invoice, $result->getCdrZip());
    $codigocdr= $cdr->getCode();

    $util->showPdf($pdf, $filename.'.pdf');
    $nc=str_pad(array_get($fac, 'numero_doc'), 6, "0", STR_PAD_LEFT);

            if($codigocdr > 4000 || $codigocdr==0){

                DocumentoFacturado::create([
                    'tipo_documento' =>'FACTURA',
                    'codigo_documento' => '01',
                    'serie' =>'F001',
                    'numeracion' =>$nc,
                    'filename'=>$filename,
                    'fecha_venta' =>new \DateTime(),
                    'estado'=>'PROCESADO',
                    'tipo_pago' =>'PEN',
                    'total' =>array_get($fac, 'cartTotal'),
                    'gravadas'  =>array_get($fac, 'cartSubTotal'),
                    'igv' =>array_get($fac, 'cartIgv')
                    ]);

                    $correlativo=DB::table('documento_correlativo')
                    ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                    ->where("documento_correlativo.iddocumento","=","01")
                    ->first();
                    $ccnumero=$correlativo->numero + 1;
                    DB::table('documento_correlativo')
                    ->where('iddocumento', '01')
                    ->update(array('dcorrelativo' =>$ccnumero));

                    $result = array("status"=>"true","message"=> $filename,"codigo"=>$codigocdr);
                    return response()->json($result);
            }
            else if($codigocdr >= 2000 and $codigocdr<= 3999){
            
            
                DocumentoFacturado::create([
                    'tipo_documento' =>'FACTURA',
                    'codigo_documento' => '01',
                    'serie' =>'F001',
                    'numeracion' =>$nc,
                    'filename'=>$filename,
                    'fecha_venta' =>new \DateTime(),
                    'estado'=>'RECHAZADO',
                    'tipo_pago' =>'PEN',
                    'total' =>array_get($fac, 'cartTotal'),
                    'gravadas'  =>array_get($fac, 'cartSubTotal'),
                    'igv' =>array_get($fac, 'cartIgv')
                    ]);
                
                    $correlativo=DB::table('documento_correlativo')
                    ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                    ->where("documento_correlativo.iddocumento","=","01")
                    ->first();
                    $ccnumero=$correlativo->numero + 1;
                    DB::table('documento_correlativo')
                    ->where('iddocumento', '01')
                    ->update(array('dcorrelativo' =>$ccnumero));

                    $result = array("status"=>"false","message"=> "Código de error:".$codigocdr."-"."   Descripción de error: ".$cdr->getDescription());       
                    return response()->json($result);
            
            }
            else{
                $result = array("status"=>"false","message"=> "Código de error:".$codigocdr."-"."   Descripción de error: ".$cdr->getDescription());
                echo json_encode($result);
            }
        
        }
        else{
            echo json_encode($util->getErrorResponse($res->getError()));
        }
        
        
    } 
else if($tip=='boleta'){


   $util = Util::getInstance();
   $client = new Client();
   $client ->setTipoDoc(array_get($fac, 'tipo_documento'))
   ->setNumDoc(array_get($fac, 'documento_identidad'))
   ->setRznSocial(array_get($fac, 'nombre_cliente'))
   ->setAddress((new Address())
   ->setDireccion(array_get($fac, 'direccion')));

    $invoice = new Invoice();
    $invoice
        ->setUblVersion('2.1')
        ->setTipoOperacion('0101')
        ->setTipoDoc('03')
        ->setSerie('B001')
        ->setCorrelativo(array_get($fac, 'numero_doc'))
        ->setFechaEmision(new \DateTime())
        ->setTipoMoneda('PEN')
        ->setClient($client)
        ->setMtoOperGravadas(array_get($fac, 'cartSubTotal'))
        ->setMtoIGV(array_get($fac, 'cartIgv'))
        ->setTotalImpuestos(array_get($fac, 'cartIgv'))
        ->setValorVenta(array_get($fac, 'cartSubTotal'))
        ->setMtoImpVenta(array_get($fac, 'cartTotal'))
        ->setCompany($util->getCompany());
       
for ($i = 0; $i < count($carrito); $i++) {

     $item = new SaleDetail();
     $item->setCodProducto(array_get($carrito[$i], 'id'))
           ->setUnidad('NIU')
           ->setDescripcion(array_get($carrito[$i], 'producto'))
           ->setCantidad(array_get($carrito[$i], 'quantity'))
           ->setMtoValorUnitario(array_get($carrito[$i], 'precio'))
           ->setMtoValorVenta(array_get($carrito[$i], 'importe'))
           ->setMtoBaseIgv(array_get($carrito[$i], 'subtotal'))
           ->setPorcentajeIgv(18)
           ->setIgv(array_get($carrito[$i], 'igv'))
           ->setTipAfeIgv('10')
           ->setTotalImpuestos(array_get($fac, 'cartIgv'))
           ->setMtoPrecioUnitario(array_get($carrito[$i], 'precio'));
     $items[]=$item;
}

$Total=NumeroALetras::convertir(array_get($fac, 'cartTotal'));
   $invoice->setDetails($items)
       ->setLegends([
           (new Legend())
               ->setCode('1000')
               ->setValue($Total
               )
       ]);
      //$see = $util->getSee(SunatEndpoints::FE_PRODUCCION);
      $see = $util->getSee(SunatEndpoints::FE_BETA);
      $res = $see->send($invoice);
      $util->writeXml($invoice, $see->getFactory()->getLastXml());
   
   
      $filename = $invoice->getName();
      $pdf = $util->getPdf($invoice);
      $util->showPdf($pdf, $filename.'.pdf');
      $nc=str_pad(array_get($fac, 'numero_doc'), 6, "0", STR_PAD_LEFT);
   
      if ($result->isSuccess()) {
   
       $filename = $invoice->getName();
       $pdf = $util->getPdf($invoice);
       $cdr = $result->getCdrResponse();
       $util->writeCdr($invoice, $result->getCdrZip());
       $codigocdr= $cdr->getCode();
   
       $util->showPdf($pdf, $filename.'.pdf');
       $nc=str_pad(array_get($fac, 'numero_doc'), 6, "0", STR_PAD_LEFT);
   
               if($codigocdr > 4000 || $codigocdr==0){
    
                        DocumentoFacturado::create([
                            'tipo_documento' =>'BOLETA',
                            'codigo_documento' => '03',
                            'serie' =>'B001',
                            'numeracion' =>$nc,
                            'filename'=>$filename,
                            'fecha_venta' =>new \DateTime(),
                            'estado'=>'PROCESADO',
                            'tipo_pago' =>'PEN',
                            'total' =>array_get($fac, 'cartTotal'),
                            'gravadas'  =>array_get($fac, 'cartSubTotal'),
                            'igv' =>array_get($fac, 'cartIgv')
                            ]);
                    
                        $correlativo=DB::table('documento_correlativo')
                        ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                        ->where("documento_correlativo.iddocumento","=","03")
                        ->first();
                        $ccnumero=$correlativo->numero + 1;
                    
                        DB::table('documento_correlativo')
                        ->where('iddocumento', '03')
                        ->update(array('dcorrelativo' =>$ccnumero));

   
                       $result = array("status"=>"true","message"=> $filename,"codigo"=>$codigocdr);
                       return response()->json($result);
               }
               else if($codigocdr >= 2000 and $codigocdr<= 3999){
               
               
                        DocumentoFacturado::create([
                            'tipo_documento' =>'BOLETA',
                            'codigo_documento' => '03',
                            'serie' =>'B001',
                            'numeracion' =>$nc,
                            'filename'=>$filename,
                            'fecha_venta' =>new \DateTime(),
                            'estado'=>'RECHAZADO',
                            'tipo_pago' =>'PEN',
                            'total' =>array_get($fac, 'cartTotal'),
                            'gravadas'  =>array_get($fac, 'cartSubTotal'),
                            'igv' =>array_get($fac, 'cartIgv')
                            ]);
                    
                        $correlativo=DB::table('documento_correlativo')
                        ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                        ->where("documento_correlativo.iddocumento","=","03")
                        ->first();
                        $ccnumero=$correlativo->numero + 1;
                    
                        DB::table('documento_correlativo')
                        ->where('iddocumento', '03')
                        ->update(array('dcorrelativo' =>$ccnumero));

   
                       $result = array("status"=>"false","message"=> "Código de error:".$codigocdr."-"."   Descripción de error: ".$cdr->getDescription());       
                       return response()->json($result);
               
               }
               else{
                   $result = array("status"=>"false","message"=> "Código de error:".$codigocdr."-"."   Descripción de error: ".$cdr->getDescription());
                   echo json_encode($result);
               }
           
           }
           else{
               echo json_encode($util->getErrorResponse($res->getError()));
           } 

}


private function listado($item){
        
        return $item;
    }

public function sentData(Request $request){

        $codigo_producto=$request->id;
        $cantidad=$request->cantidad;
        $id_usuario=$request->user;
        $precio=$request->precio;

$distriId=0;
        //hacer inner con guia y poner diferente de desaprobad0

           $products =  DB::table('presentacionventa')
               ->join('producto', 'presentacionventa.idproducto', '=', 'producto.idproducto')
                ->join('entrada', 'producto.idproducto', '=', 'entrada.idproducto')
                ->select('presentacionventa.nuevonombre')
                ->where("presentacionventa.idpresentacionventa", "=", "{$codigo_producto}")
->where("presentacionventa.destino", "=", "{$distriId}")
                ->first();
           
      $entrada= Presentacionventa::where('idpresentacionventa', $codigo_producto)->first();

          $cart = \Session::get('cart');
        $entrada->quantity = $cantidad;
        $entrada->formapago="";  
        $entrada->nombre=$products->nuevonombre;
        $entrada->precioMaximo=$precio;
 $cart[$entrada->idpresentacionventa] = $entrada;
        \Session::put('cart', $cart);



 //$cart = \Session::get('cart');



/*
$correlativo=DB::table('documento_correlativo')
                ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                ->where("documento_correlativo.iddocumento","=","03")
                ->first();
$ccnumero=$correlativo->numero + 1;

$nc=str_pad($ccnumero, 8, "0", STR_PAD_LEFT);


      $order = Ordencompra::create([
          
          'formapago' => '',
          'idcliente' => '',
          'idusuario' => $id_usuario,
          'subtotal' => $subtotal,
          'pago' => 0,
          'total' => $subtotal,
          'estado'=>1,
          'estadoorden'=>1,
          'tipoventa'=>1,
          'tipodocumento'=>'03
          ',
          'correlativo'=>$nc


          
      ]);




DB::table('documento_correlativo')
            ->where('iddocumento', '01')
            ->update(array('dcorrelativo' =>$ccnumero));


*/



}

public function documento_formato($radio){
    $date = date('Y-m-d');
  

    if($radio=='factura'){
        $correlativo=DB::table('documento_correlativo')
                ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
                ->where("documento_correlativo.iddocumento","=","01")
                ->first();
        $ccnumero=$correlativo->numero + 1;

        $nc=str_pad($ccnumero, 6, "0", STR_PAD_LEFT);

        return response()
        ->json([
            'serie' => 'F001',
            'correlativo' => $nc,
            'tipo'=>'6',
            'fecha'=>$date
        ]);
        


    }else if ($radio=='boleta'){

        $correlativo=DB::table('documento_correlativo')
        ->select(DB::raw("max(documento_correlativo.dcorrelativo) as numero"))
        ->where("documento_correlativo.iddocumento","=","03")
        ->first();
        $ccnumero=$correlativo->numero + 1;

        $nc=str_pad($ccnumero, 6, "0", STR_PAD_LEFT);


        return response()
        ->json([
            'serie' => 'B001',
            'correlativo' => $nc,
            'tipo'=>'1',
            'fecha'=>$date
        ]);
    }
      
   


}



public function buscar(Request $request)
{

   
    $documento=$request->documento;

    $tipo=$request->tipo;


    if($tipo =='6'){

      
        $company = new \Sunat\Sunat( true, true );
          
        $ruc = $documento;
       
        $search1 = $company->search( $ruc );
        if( $search1->success == true )
          {
              $data=[];    
         
      $cliente=$search1->result->razon_social;
      $direccion=$search1->result->direccion;
      
      return response()
      ->json([
          'cliente' => $cliente,
          'direccion' => $direccion,
      ]);
      
      
          }



    }else if($tipo=='1'){



        $servir = new \Reniec\reniec();
	
        $dni =$documento;
      
      
      
        $search = $servir->search( $dni );

      
      
                if( $search->success == true )
                {

                   
                  
                  $cliente=$search->result->Nombres.' '.$search->result->apellidos;
                  $direccion=$search->result->Departamento.'-'.$search->result->Provincia.'-'.$search->result->Distrito;
                  
                 
                  return response()
                  ->json([
                      'cliente' => $cliente,
                      'direccion' => $direccion,
                  ]);
                  
                  
                }
        

    }

  /*

  $hostname = explode(".", gethostbyaddr($_SERVER['REMOTE_ADDR']));
  dd($hostname[0]);  */


}



    public function productoventa(Request $request){


        $valor=$request->busqueda;
        $tipo=$request->radio;

  if($tipo==1){


    $products = DB::table('presentacionventa') 
                            
    ->join('producto', 'presentacionventa.idproducto', '=', 'producto.idproducto')
    ->join('entrada', 'producto.idproducto', '=', 'entrada.idproducto')

     ->select(DB::raw("presentacionventa.idpresentacionventa,
     presentacionventa.idproducto,presentacionventa.nuevonombre,
     presentacionventa.idunidadmedida,presentacionventa.precioventa"))
     ->where("entrada.stock",">","0")  
     ->Where('presentacionventa.idproducto', 'like', '%' . $valor . '%') 
     ->groupBy('presentacionventa.idpresentacionventa')
     ->groupBy('presentacionventa.idproducto')
     ->groupBy('presentacionventa.nuevonombre')
     ->groupBy('presentacionventa.idunidadmedida')
     ->groupBy('presentacionventa.precioventa')

->get();

$data=[];    
foreach ($products as $key => $value) {

$data[$key] =[
'id'=>$value->idpresentacionventa,
'codigo' =>$value->idproducto,
'producto'=>$value->nuevonombre,
'unidad'=>$value->idunidadmedida,
'precio'=>$value->precioventa

];

}

return response()->json($data);


  }else{



    $products = DB::table('presentacionventa') 
                            
    ->join('producto', 'presentacionventa.idproducto', '=', 'producto.idproducto')
    ->join('entrada', 'producto.idproducto', '=', 'entrada.idproducto')

     ->select(DB::raw("presentacionventa.idpresentacionventa,
     presentacionventa.idproducto,presentacionventa.nuevonombre,
     presentacionventa.idunidadmedida,presentacionventa.precioventa"))
     ->where('entrada.stock','>','0')  
     ->Where('presentacionventa.nuevonombre', 'like', '%' . $valor . '%')
     ->groupBy('presentacionventa.idpresentacionventa')
     ->groupBy('presentacionventa.idproducto')
     ->groupBy('presentacionventa.nuevonombre')
     ->groupBy('presentacionventa.idunidadmedida')
     ->groupBy('presentacionventa.precioventa')


->get();

$data=[];    
foreach ($products as $key => $value) {

$data[$key] =[

    'id'=>$value->idpresentacionventa,
    'codigo' =>$value->idproducto,
    'producto'=>$value->nuevonombre,
    'unidad'=>$value->idunidadmedida,
    'precio'=>$value->precioventa

];

}

return response()->json($data);


  }


       
       }
       


public function facturados($type){
    $today = Carbon::now();
    

if($type=='TODOS'){




    $doc = DB::table('documentos_facturados') 
    ->select(DB::raw("documentos_facturados.*"))
    ->where(DB::raw('MONTH(fecha_venta)'), '=', $today->month)
    ->orderBy('documentos_facturados.numeracion', 'desc')
    ->get();

    
       $data=[]; 
       foreach ($doc as $key => $value) {



       $data[$key] =[

       'tipo_documento'=>$value->tipo_documento,
       'codigo_documento' =>$value->codigo_documento,
       'serie'=>$value->serie,
       'numeracion'=>$value->numeracion,
       'filename'=>$value->filename,
       'fecha_venta'=>$value->fecha_venta,
       'estado'=>$value->estado,
       'tipo_pago'=>$value->tipo_pago,
       'total'=>$value->total,
       'gravadas'=>$value->gravadas,
       'igv'=>$value->igv,
       

       ];





       }




           return response()->json($data);

}
else if($type=='FACTURA'){



    $doc = DB::table('documentos_facturados') 
    ->select(DB::raw("documentos_facturados.*"))
    ->where("documentos_facturados.tipo_documento","=","FACTURA")
    ->where(DB::raw('MONTH(fecha_venta)'), '=', $today->month)
    ->orderBy('documentos_facturados.numeracion', 'desc')
    ->get();

    
       $data=[]; 
       foreach ($doc as $key => $value) {



       $data[$key] =[

       'tipo_documento'=>$value->tipo_documento,
       'codigo_documento' =>$value->codigo_documento,
       'serie'=>$value->serie,
       'numeracion'=>$value->numeracion,
       'filename'=>$value->filename,
       'fecha_venta'=>$value->fecha_venta,
       'estado'=>$value->estado,
       'tipo_pago'=>$value->tipo_pago,
       'total'=>$value->total,
       'gravadas'=>$value->gravadas,
       'igv'=>$value->igv,
       

       ];





       }




           return response()->json($data);
}
else if($type=='BOLETA'){
    
    
    $doc = DB::table('documentos_facturados') 
    ->select(DB::raw("documentos_facturados.*"))
    ->where("documentos_facturados.tipo_documento","=","BOLETA")
    ->where(DB::raw('MONTH(fecha_venta)'), '=', $today->month)
    ->orderBy('documentos_facturados.numeracion', 'desc')
    ->get();

    
       $data=[]; 
       foreach ($doc as $key => $value) {



       $data[$key] =[

       'tipo_documento'=>$value->tipo_documento,
       'codigo_documento' =>$value->codigo_documento,
       'serie'=>$value->serie,
       'numeracion'=>$value->numeracion,
       'filename'=>$value->filename,
       'fecha_venta'=>$value->fecha_venta,
       'estado'=>$value->estado,
       'tipo_pago'=>$value->tipo_pago,
       'total'=>$value->total,
       'gravadas'=>$value->gravadas,
       'igv'=>$value->igv,
       

       ];





       }




           return response()->json($data);
    
    
    
    }

}


 public function baja(Request $request){

    $codigo_documento=$request->codigo_documento;
    $serie=$request->serie;
    $numeracion= $request->numeracion;
    $fecha_venta=$request->fecha_venta;




    $util = Util::getInstance();

    //$voided = $util->getVoided();


  $detial1 = new VoidedDetail();
        $detial1->setTipoDoc($codigo_documento)
            ->setSerie($serie)
            ->setCorrelativo($numeracion)
            ->setDesMotivoBaja('ERROR DE SISTEMA');


        $voided = new Voided();
        $voided->setCorrelativo('1')
            ->setFecComunicacion(new \DateTime())
            ->setFecGeneracion(new \DateTime())
            ->setCompany($util->getCompany())
            ->setDetails([$detial1]);
// Envio a SUNAT.
$see = $util->getSee(SunatEndpoints::FE_PRODUCCION);

$res = $see->send($voided);
$util->writeXml($voided, $see->getFactory()->getLastXml());

if ($res->isSuccess()) {
    /**@var $res \Greenter\Model\Response\SummaryResult*/
    $ticket = $res->getTicket();
    echo 'Ticket :<strong>' . $ticket .'</strong>';

    $result = $see->getStatus($ticket);
    if ($result->isSuccess()) {
        $cdr = $result->getCdrResponse();
        $util->writeCdr($voided, $result->getCdrZip());

        $util->showResponse($voided, $cdr);

        
        DB::table('documentos_facturados')
        ->where('codigo_documento', $codigo_documento)
        ->where('serie', $serie)
        ->where('numeracion', $numeracion)
        ->update(array('estado' =>'ANULADO'));


    } else {
        echo $util->getErrorResponse($result->getError());
    }
} else {
    echo $util->getErrorResponse($res->getError());
}

 }   

}
