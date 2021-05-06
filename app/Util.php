<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\StoreTrait;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Response\CdrResponse;
use App\HtmlReport;
use App\PdfReport;
use Greenter\See;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use App\Producto;
class Util extends Model
{
 use StoreTrait;

    private static $current;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }

        return self::$current;
    }

    /**
     * @param string $endpoint
     * @return See
     */
    public function getSee($endpoint)
    {
        $see = new See();
        $see->setService($endpoint);
//        $see->setCodeProvider(new XmlErrorCodeProvider());
        $see->setCertificate(file_get_contents(__DIR__ . '/../resources/cert.pem'));
        $see->setCredentials('20536226231ALVI2019', '@lv12019A');
       // $see->setCachePath(__DIR__ . '/../cache');

        return $see;
    }

    public function showResponse(DocumentInterface $document, CdrResponse $cdr)
    {
        $filename = $document->getName();

        require __DIR__.'/../resources/views/response.php';
    }

    public function getErrorResponse(\Greenter\Model\Response\Error $error)
    {

        $mensaje= $error->getMessage();
        $result = <<<HTML
        <h2 class="text-danger">Error:</h2><br>
        <b>Código:</b>{$error->getCode()}<br>
        <b>Descripción:</b>{$error->getMessage()}<br>
HTML;

        return $mensaje;
    }

    public function writeXml(DocumentInterface $document, $xml)
    {
        $this->writeFile($document->getName().'.xml', $xml);
    }

    public function writeCdr(DocumentInterface $document, $zip)
    {
        $this->writeFile('R-'.$document->getName().'.zip', $zip);
    }

    public function writeFile($filename, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }
        file_put_contents(__DIR__.'/../public/files/'.$filename, $content);
    }

    public function getPdf(DocumentInterface $document)
    {   
        $dc = array_get($document, 'company');
       // dd($document->getdetails());
        $connector = new NetworkPrintConnector("192.168.1.10",9100);
        $printer = new Printer($connector);
        # Vamos a alinear al centro lo próximo que imprimamos
        $printer->setJustification(Printer::JUSTIFY_CENTER);


        $printer->text($document->getCompany()->getrazonSocial() . "\n");
        $printer->text("RUC: " .$document->getCompany()->getruc(). "\n");
        $printer->text($document->getCompany()->getaddress()->getdireccion() . "\n");
     
        #Tipo de comprobante
        $printer->text(($document->gettipoDoc()==='01'?'FACTURA':'BOLETA')." "."ELECTRONICA"."\n");
        $printer->text($document->getserie()."-".$document->getcorrelativo()."\n");

        #La fecha también
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text('FECHA EMISION: '.date("Y-m-d H:i:s") . "\n");
        $printer->text(($document->getclient()->getTipoDoc()==='6'?'RUC:':'DNI:')." ".$document->getclient()->getNumDoc()."\n");
        $printer->text(('CLIENTE:')." ".$document->getclient()->getrznSocial()."\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("----------------------------------------" ."\n");
        $printer->text("Cant. Descripcion         P.Unit Importe" ."\n");
        $printer->text("----------------------------------------" ."\n");
        
        $opgravada = 0;
        $igv = 0;
        $total = 0;


        foreach ($document->getdetails() as $producto) {
            $opgravada += $producto->getmtoBaseIgv();
            $igv += $producto->getigv();
            $total += $producto->getcantidad() * $producto->getmtoValorUnitario();
            
            /*Alinear a la izquierda para la cantidad y el nombre*/
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text($producto->getcantidad() . " " .($producto->getunidad()==='NIU'?'UND':$producto->getunidad())." ". $producto->getdescripcion() . "\n");
         
            /*Y a la derecha para el importe*/
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text($producto->getmtoPrecioUnitario() .'   '.$producto->getmtoValorVenta() . "\n");
        }

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("----------------------------------------" ."\n");

      
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("OP GRAVADA:"."                    "."S/".number_format($opgravada, 2, '.', '') . "\n");
        $printer->text("IGV:"."                            "."S/".number_format($igv, 2, '.', '') . "\n");
        $printer->text("TOTAL:"."                         "."S/".number_format($total, 2, '.', '') . "\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("SON: ". $document->getlegends()[0]->getvalue() ." "."\n");
        $printer->text("----------------------------------------" ."\n");
        $printer->text("GRACIAS POR SU PREFERENCIA" ."\n");
        $printer->text("----------------------------------------" ."\n");
        $hash = $this->getHash($document);
        $params = self::getParametersPdf();
        $params['system']['hash'] = $hash;


        $printer->text("Representacion impresa de la "."\n");
        $printer->text(($document->gettipoDoc()==='01'?'FACTURA':'BOLETA')." "."ELECTRONICA"."\n");
        $printer->text("Codigo Hash"."\n");
        $printer->text( $hash ."\n");

        $printer->feed(3);
        $printer->cut();
        $printer->pulse();
        $printer->close();

    /*    $html = new HtmlReport('', [
            'cache' => __DIR__ . '/../bootstrap/cache',
            'strict_variables' => true,
        ]);

        $template = $this->getTemplate($document);
        if ($template) {
            $html->setTemplate($template);
        }

        $render = new PdfReport($html);
        $render->setOptions( [
            'no-outline',
            'page-width' => '70mm',
            'page-height' => '270mm',
            'margin-left' => 0,
            'margin-right' => 0,
            'footer-html' => __DIR__.'/../resources/views/footer.html',
        ]);
        $binPath = self::getPathBin();
        if (file_exists($binPath)) {
            $render->setBinPath($binPath);
            
        }
        $hash = $this->getHash($document);
        $params = self::getParametersPdf();
        $params['system']['hash'] = $hash;
        $params['user']['footer'] = '<div><strong> Consulte en </strong> <a href="http://e-consulta.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm">SUNAT</a></div><br>';

        $pdf = $render->render($document, $params);

        if ($pdf === false) {
            $error = $render->getExporter()->getError();
            echo 'Error: '.$error;
            exit();
        }

        // Write html
        $this->writeFile($document->getName().'.html', $render->getHtml());
        
        

        return $pdf;*/
    }

    public static function generator($item, $count)
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $item;
        }

        return $items;
    }

    public function showPdf($content, $filename)
    {
        $this->writeFile($filename, $content);
      /*  header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($content));

        echo $content;*/
    }

    public static function getPathBin()
    {
        $path = __DIR__.'/../vendor/bin/wkhtmltopdf';
        if (self::isWindows()) {
            $path .= '.exe';
        }

        return $path;
    }

    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function inPath($command) {
        $whereIsCommand = self::isWindows() ? 'where' : 'which';

        $process = proc_open(
            "$whereIsCommand $command",
            array(
                0 => array("pipe", "r"), //STDIN
                1 => array("pipe", "w"), //STDOUT
                2 => array("pipe", "w"), //STDERR
            ),
            $pipes
        );
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            return $stdout != '';
        }

        return false;
    }

    private function getTemplate($document)
    {
        $className = get_class($document);

        switch ($className) {
            case \Greenter\Model\Retention\Retention::class:
                $name = 'retention';
                break;
            case \Greenter\Model\Perception\Perception::class:
                $name = 'perception';
                break;
            case \Greenter\Model\Despatch\Despatch::class:
                $name = 'despatch';
                break;
            case \Greenter\Model\Summary\Summary::class:
                $name = 'summary';
                break;
            case \Greenter\Model\Voided\Voided::class:
            case \Greenter\Model\Voided\Reversion::class:
                $name = 'voided';
                break;
            default:
                return '';
        }

        return $name.'.html.twig';
    }

    private function getHash(DocumentInterface $document)
    {
        $see = $this->getSee('');
        $xml = $see->getXmlSigned($document);

        $hash = (new \Greenter\Report\XmlUtils())->getHashSign($xml);

        return $hash;
    }

    private static function getParametersPdf()
    {
        $logo = file_get_contents(__DIR__.'/../resources/logo.png');

        return [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header' => 'Telf: <b>(01) 593 - 3097</b>',
                'extras' => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => 'L&K PERSONAL'],
                ],
            ]
        ];
    }
}
