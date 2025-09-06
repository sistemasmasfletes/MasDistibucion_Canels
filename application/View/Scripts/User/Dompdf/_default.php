<?php
/* @var $view Model3_View*/
//require_once 'dompdf/dompdf_config.inc.php';<link rel="stylesheet" href="'.$prueba.'" type="text/css" />
//ini_set("memory_limit", "32M");
//ob_start();
if ($view->packs)
{
    $cont = 0;
    foreach ($view->packs as $p)
    {
       $cont += $p->getNumPackage();
    }
}
$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml"><head>
    <style> 
    body {
        margin: 0;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 10px;
        line-height: 18px;
        color: #333333;
        background-color: #ffffff;
      }
    table {
        max-width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        }

    </style>
    </head><body>';
	//@todo: remover estilos que no se utilizan
	
$table = "";
$content1 = "";
$content2 = "";

$dias = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

if ($view->order)
{
    $indice = 0;
    for ($i = 1; $i <= $cont; $i++)
    {
        $seqHtml = $seqShippedHtml = '<table>';
        $pointstotal = count($view->sequentials);
		$contador = 1;
		foreach($view->sequentials as $seq){
			
			if($contador == 1){ 
				$dir1 = utf8_decode($seq->getRoutePoint()->getPoint()->getFullAddress()); 
    		}
			if($contador == $pointstotal){ 
				$dir2 = utf8_decode($seq->getRoutePoint()->getPoint()->getFullAddress()); 
				$pname = utf8_decode($seq->getRoutePoint()->getPoint()->getName());
	            $position = utf8_decode($seq->getRoutePoint()->getOrder());			
			}
            if($seq->getType()==1){
                $seqHtml .='<tr>';
                $seqHtml .='<td>'.utf8_decode($seq->getRoutePoint()->getPoint()->getFullAddress()).'</td>';
                //$seqHtml .='<td style="color:#B00700;font-size: 10px;">'.$seq->getShippingDate()->format('Y-m-d H:i').'</td>';
                $seqHtml .='</tr>';
            }else{
                $seqShippedHtml .='<tr>';
                $seqShippedHtml .='<td>'.utf8_decode($seq->getRoutePoint()->getPoint()->getFullAddress()).'</td>';
                //$seqShippedHtml .='<td style="color:#B00700;font-size: 10px;">'.$seq->getShippingDate()->format('Y-m-d H:i').'</td>';
                $seqShippedHtml .='</tr>';
            }
            //$lastdate =$seq->getShippingDate()->format('Y-m-d');
            
			$lastdate = $dias[intval($seq->getShippingDate()->format('w'))]
            		." ".$seq->getShippingDate()->format('d')." de ".$meses[intval($seq->getShippingDate()->format('m')-1)];
            
            $contador++;
            
        }
        $seqHtml .='</table>';
        $seqShippedHtml .='</table>';
        /*se agrega el punto para concatenar cadena y mostrar todos los producots*/
        $table ='
                <table style="width:100%;border: solid #000 1px; text-align:center; padding:0;">
                <thead>
        		<tr>
        			<th colspan=2 style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">Datos de Embalaje</th>
        		</tr>
                <tr>
                    <!--th>Folio</th>
                    <th>Envio de</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Ruta</th>
                    <th>Unidades</th>
                    <th>Precio unidad</th-->
                    <th>Unidades</th>
                    <th>Descripcion</th>
        		</tr>
                </thead>
                
                <tr>
                    <!--td>' . /*$view->order->getId() . '</td>
                    <td>' . $view->order->getSeller()->getCommercialName() . '</td>
                    <td>' . $seqHtml. '</td>
                    <td>' . $seqShippedHtml. '</td>
                    <td>' . $view->rout[0]->getRoute()->getName() .*/ '</td-->
                    <td>' . $i . ' de ' . $cont . '</td>                    
                    <td>' . /*number_format($view->packs[$indice]->getPrice(),2) .*/ $view->packs[$indice]->getNamePackage() . '</td>';
                    if($i == $view->packs[$indice]->getNumPackage())
                        $indice++;
     $table.='</tr>';
     $enviador = utf8_decode($view->order->getSeller()->getCommercialName());
     $comprador = (strpos($view->order->getBuyer()->getCommercialName(),'No Registrado'))?$pname:utf8_decode($view->order->getBuyer()->getCommercialName());
     $cname = utf8_decode($view->order->getBuyer()->getFirstName().' '.$view->order->getBuyer()->getLastName());
     $ctel = $view->order->getBuyer()->getCellPhone();
     $ename = utf8_decode($view->order->getSeller()->getFirstName().' '.$view->order->getSeller()->getLastName());
     $etel = $view->order->getSeller()->getCellPhone();
     $comments = utf8_decode($view->order->getComments());
      
     $table .='
        </table><br />';
     if(count($view->products) > 1){
     	
	     $table .='
	     		<table style="width:100%;border: solid #000 1px; text-align:center; padding:0;">
	        		<tr>
	        			<th colspan=2 style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">Datos del Producto</th>
	        		</tr>
	     			<tr>
	     				<td style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">Cantidad</td>
	     				<td style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">Descripción</td>
	     			</tr>';
	    foreach ($view->products as $prd){
	     	 
	     	$table .= '<tr>
	     				<td style="border: solid #000 1px;">'.$prd->getQuantity().'</td>
	     				<td style="border: solid #000 1px;">'.utf8_decode($prd->getProduct()->getName()).'</td>
	     			</tr>';
	    }
	    
    	$table .='</table><br />';
     	}
     
    }
}
$content1 .= '
			<table style="width:90%;border: solid #000 1px; text-align:center; padding:0;">
				<tr>
					<td colspan=3 style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">TALÓN DE ENTREGA</td>
				</tr>
				<tr>
					<td style="width:40%;">'.$view->qrcode.'</td>
					<td style="width:38%;">
						<table style="width:100%; font-size:2em; font-weight:bold;">
							<tr><td style="text-align:center; height:60px; background-color:#6AAD8A;">No. de Orden: '.$view->order->getId().'</td></tr>
							<tr><td style="text-align:center; height:60px;">Fecha de entrega: '.$lastdate.'</td></tr>
							<tr><td style="text-align:center; height:60px;">Secuencia de entrega: '.$position.'</td></tr>
						</table>
					</td>
					<td style="width:20%;">'.'<img style="width:200px; height:90px; margin:0;" src="../'.$view->getBaseUrl() . '/images/logo.png" />'.'</td>
				</tr>
				<tr>
					<td colspan=3 style="border-top: solid #000 1px; text-align:left;"><strong>COMENTARIOS DE LA COMPRA: </strong>'.$comments.'</td>
				</tr>
			</table>
		<br />';
		
$content1 .= '
	<table style="border: solid #000 1px;width:99%; text-align:center;">
		<tr>
			<td colspan=4 style="border: solid #000 1px; background-color:#6AAD8A; width:50%; font-weight:bold;">Datos fiscales del cliente Destinatario</td>
			<td colspan=4 style="border: solid #000 1px; background-color:#6AAD8A; font-weight:bold;">Datos fiscales del cliente Remitente</td>
		</tr>	
		<tr>
			<td style="border: solid #000 1px; width:15%; font-weight:bold;">NOMBRE:</td>
			<td colspan=3 style="border: solid #000 1px; width:28%;">'.$comprador.'</td>
			<td style="border: solid #000 1px; width:15%; font-weight:bold;">NOMBRE:</td>
			<td colspan=3 style="border: solid #000 1px; width:30%;">'.$enviador.'</td>
		</tr>
		<tr>
			<td style="border: solid #000 1px; width:20%; font-weight:bold;">DIRECCION:</td>
			<td colspan=3 style="border: solid #000 1px;">'.$dir2.'</td>
			<td style="border: solid #000 1px; width:20%; font-weight:bold;">DIRECCION:</td>
			<td colspan=3 style="border: solid #000 1px;">'.$dir1.'</td>
		</tr>
		<tr>
			<td style="border: solid #000 1px; width:20%; font-weight:bold;">CONTACTO:</td>
			<td style="border: solid #000 1px;">'.$cname.'</td>
			<td style="border: solid #000 1px; width:5%; font-weight:bold;">TEL:</td>
			<td style="border: solid #000 1px;">'.$ctel.'</td>
			<td style="border: solid #000 1px; width:20%; font-weight:bold;">CONTACTO:</td>
			<td style="border: solid #000 1px;">'.$ename.'</td>
			<td style="border: solid #000 1px; width:5%; font-weight:bold;">TEL:</td>
			<td style="border: solid #000 1px;">'.$etel.'</td>
		</tr>
	</table><br />		
		
		';
$content1 .= $table;
$content2 .= $content1;




/*$content .= '
		<table style="width:100%;border:solid #000 1px; text-align: center;">
			<tr>
				<td colspan=7 style="background-color:#6AAD8A; font-weight:bold;">Datos de Metódo de Pago</td>
			</tr>
			<tr>
				<td style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">Fecha de pago</td>
				<td colspan=4 style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">Modalidad de pago</td>
				<td colspan=2 style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">Cantidad a pagar</td>
			</tr>
			<tr>
				<td rowspan=2 style="border:solid #000 1px;"></td>
				<td style="border:solid #000 1px;">Efectivo</td>
				<td style="border:solid #000 1px;">Transferencia</td>
				<td style="border:solid #000 1px;">Tarjeta de credito</td>
				<td style="border:solid #000 1px;">Credito</td>
				<td rowspan=2 colspan=2 style="border:solid #000 1px;"></td>
				<tr>
					<td style="border:solid #000 1px; height:15px;"></td>
					<td style="border:solid #000 1px;"></td>
					<td style="border:solid #000 1px;"></td>
					<td style="border:solid #000 1px;"></td>
				</tr>
			</tr>
			<tr>
				<td colspan=2 style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">No de pago</td>
				<td colspan=5 style="border:solid #000 1px;"></td>
			</tr>
		</table><br />
		
		<table style="width:100%;border:solid #000 1px; text-align: center;">
			<tr>
				<td style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold; width:50%;">RECIBIDO POR:</td>
				<td style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">ENTREGADO POR:</td>
			</tr>
			<tr>
				<td style="border:solid #000 1px; font-weight:bold;">NOMBRE</td>
				<td style="border:solid #000 1px; font-weight:bold;">NOMBRE</td>
			</tr>
			<tr>
				<td style="border:solid #000 1px;"></td>
				<td style="border:solid #000 1px; font-weight:bold;height:50px;"></td>
			</tr>
			<tr>
				<td style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">FIRMA</td>
				<td style="border:solid #000 1px;background-color:#6AAD8A; font-weight:bold;">FIRMA</td>
			</tr>
			<tr>
				<td style="border:solid #000 1px;height:50px;"></td>
				<td style="border:solid #000 1px;"></td>
			</tr>
			<tr>
				<td colspan=2 >Este documento no es un  comprobante fiscal. <br /> De requerir factura solicitarla a masdistribucion.ventas@gmail.com</td>
			</tr>
		</table>
		';*/
$content .= $content1 .$content2;
$content .= '</body></html>';

//echo $content;
$dompdf = new DOMPDF();
$dompdf->load_html($content);
$dompdf->render();
$dompdf->stream("etiquetas.pdf");
