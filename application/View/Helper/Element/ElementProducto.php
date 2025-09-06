<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ElementProducto
 *
 * @author usuario
 */
class View_Helper_Element_ElementProducto extends Model3_View_Helper
{

    const IMAGE_WIDTH = 400;
    const IMAGE_HEIGHT = 400;

    public function build($elemento, $view, $editMode, $preview)
    {
        $cellsImg = '';
        $tienda = false;
        $idProducto = 0;
        $product = false;
        $variantes = false;
        $pralImg = false;
        $catalog = false;
        $pralImghtml = '';
        $content_html = '';
        $zoomAdjustX = 0;
        $zoomAdjustY = 0;

        $xml = simplexml_load_string($elemento['contenido']);

        if ($xml->contenido->id_producto != '')
        {
            $idProducto = $xml->contenido->id_producto;
        }

        $resRet = self::prepareInfoProduct($idProducto, $view, $view->idSitio, $elemento['id'], $editMode, $product, $catalog, $pralImg, $pralImghtml, $cellsImg, $tienda, $zoomAdjustX, $zoomAdjustY, $variantes);
        if ($resRet !== false)
        {
            if (($editMode == true) || ($product->orderStatus && $preview == true) || ($product->orderStatus && !$editMode && !$preview))
            {
                $content_html = self::buildHTMLProductClearView($product, $view, $pralImg, $pralImghtml, $cellsImg, $idProducto, $elemento['id'], $editMode, $view->nomFix, $catalog, $tienda, $preview, $zoomAdjustX, $zoomAdjustY, $variantes);
                $content_html .= '<input type="hidden" id="adjustX" value="' . $zoomAdjustX . '"/>';
                $content_html .= '<input type="hidden" id="adjustY" value="' . $zoomAdjustY . '"/>';
            }
        }
        else
        {
            $mensaje = ($editMode === true) ? 'Producto no encontrado' : 'Este producto no esta disponible por el momento';
            $content_html = '<div class="elementContent" ><div class="notice" style="text-align: center;"> ' . $mensaje . ' </div></div>';
        }

        return $content_html;
    }

    /**
     * Prepara la informacion para construir el elemento producto
     * @param int $idProducto   Id del producto seleccionado
     * @param int $idSitio
     * @param int $elementoId
     * @param bool $editMode
     * @param <type> $product       Variable de salida. Registro del producto
     * @param <type> $catalog       Variable de salida. Registro del catalogo
     * @param string $pralImg       Variable de salida. Registro de la imagen principal a mostrar
     * @param <type> $pralImghtml   Variable de salida. String HTML de la imagen principal a mostrar
     * @param <type> $cellsImg      Variable de salida. String HTML con la tira de imagenes secundarias del producto
     * @param <type> $tienda        Variable de salida. REgistro de la tienda
     * @return bool             true en caso deexistir y no presentar problemas, false en caso contrario
     */
    static function prepareInfoProduct($product, $view, &$pralImg, &$pralImghtml, &$cellsImg, &$tienda, &$zoomAdjustX, &$zoomAdjustY, &$variantes)
    {
        $cellsImg = '';
        $pralImg = false;
        $catalog = false;
        $zoomAdjustX = 0;
        $zoomAdjustY = 0;

        if($product != null && $product instanceof DefaultDb_Entities_Product)
        {
            $images = $product->getImages();
            if($images != false)
            {
                foreach($images  as $i)
                {
                    echo $view->ManagerImages()->thumbImg(
                            $i->getPath(),
                            $product->getName(),
                            $product->getName(),
                            '',
                            '205',
                            '120'
                            );
                }
            }
        }
        
        

        //$product = $productosAdapter->getProductById($idProducto);
        //obtenemos las variantes
        //$variantes = $variantesAdapter->find('id_producto', $idProducto);
        //Se obtienen las imagenes del producto
        //$images = $imagenesProductosAdapter->getImageBySitioAndProduct($idSitio, $idProducto);
        //$images = ($images == false) ? array() : $images;
        //Se obtiene la imagen principal, en caso de existir, se obtiene su html o la imagen default
        //$pralImg = (count($images) >= 1) ? $images[0] : false;
        //$pralImghtml = (($pralImg != false) ? $pralImg->thumb : '<img alt="*" src="' . $view->getBaseUrlPublic() . '/FixImages/noImage/0/' . $idSitio . '"/>');
        //De todas las imagenes que tiene el producto, salta la primera, y obtiene las siguientes 4
        //Puede cambiarse a opciones_sitio numero de imagenes por producto
//        $numImgMin = 9;
//        $idPrincipalImg = (($pralImg != false) ? $pralImg->id : 0);
//        $widthTD = 100 / $numImgMin;        //Porcentaje de ancho de las celdas para las imagenes secundarias
//        
//        for($idx = 1;  ($idx <= $numImgMin) && ($idx < count($images)); $idx++)
//        {       
//            $rutaImagenChica = $rutaImagenGrande = $images[$idx]->ruta;
//            $location = strpos($rutaImagenGrande,'http://');
//            $server = '';            
//
//            if($location === false)
//            {
//                $server = (PRODUCCION == 0) ? 'http://localhost/' : 'http://www.storesfactory.com/';
//               
//                $rutaImagenGrande = $images[$idx]->ruta;
//                $rutaImagenGrande = $server . $rutaImagenGrande;
//                $rutaImagenChica = $images[$idx]->thumb;
//                $rutaImagenChica = $server . $rutaImagenChica;
//            }
//            $imgToShow = '<img alt="*" src="' . $view->getBaseUrl() . '/FixImages/imagesGalleryCatalog/0/' . $images[$idx]->id_producto . '/' . $images[$idx]->id_sitio . '/' . $images[$idx]->index_imagen . '" style="max-height: 80px; max-width: 80px;" />';
//
//            $cellsImg .= '<div class="thumbPicture-sf">';
//            $cellsImg .= '<div class="minimg  imgWrap" id="minimg_' . $elementoId . '_' . $images[$idx]->id . '" onclick="changeImg(\'minimg_' . $elementoId . '_' . $images[$idx]->id . '\', \'imgPral_' . $elementoId . '_' . $idPrincipalImg . '\');">';
//            $cellsImg .= $imgToShow;
//
//            $cellsImg .= '<span id="minimg_' . $elementoId . '_' . $images[$idx]->id . '_bigImage" style="display: none;">
//                                        ' . $rutaImagenGrande . '
//                                    </span>';
//
//            $cellsImg .= '<span id="minimg_' . $elementoId . '_' . $images[$idx]->id . '_smallImage" style="display: none;">
//                                        ' . $rutaImagenChica . '
//                                    </span>';
//
//            $cellsImg .= '<span id="minimg_' . $elementoId . '_' . $images[$idx]->id . '_options" style="display: none;">
//                                        ' . $view->getBaseUrl() . ' ' . $images[$idx]->id_producto . ' ' . $images[$idx]->id_sitio . ' ' . $images[$idx]->index_imagen . '
//                                    </span>';
//
//            $cellsImg .= '</div></div>';
//            
//        }

        return;
    }

    /**
     * Contruye el HTML de un producto con las variables recibidas por parametro.
     * @param <type> $product                   Objeto stdClass de un producto
     * @param <type> $pralImg                   Objeto stdClass de la imagen principal
     * @param <type> $pralImghtml               HTML de la imagen principal a presentar, puede existir o ser el 'no image'
     * @param string $cellsImg                  HTML de celdas en las que se incrustan las imagenes secundarias
     * @param int $idProducto                   Id del producto
     * @param int $elementoId                   Id del elemento al que esta asociado el producto
     * @param bool  $edicion                    Modo en que se encuentra la vista a mostrar del objeto
     * @param <type> $catalogo                  Arreglo asociativo que representa un catalogo al que pertenece el producto
     * @param <type> $tienda                    Objeto stdClass de la tienda en caso de existir | false si no
     * @param string $controller                Cadena con el nombre del controlador para generar el boton de regresar al catalogo en el template 3
     * @param string $action                    Nombre de la action para generar el boton de regresar al catalogo en el template 3
     * @param array $params                     Arreglo con los parametros de la pagina para generar el boton regresar al catalogo en el template 3
     * @return string                           HTML resultado de la vista de un producto
     */
    static function buildHTMLProducto($product, $view, $pralImg, $pralImghtml, $cellsImg, $idProducto, $elementoId, $edicion, $nomFix, $catalogo, $tienda, $preview, $zoomAdjustX, $zoomAdjustY, $variantes, $controller = '', $action = '', $params = null)
    {
        $haveTags = '';
        $infoProduct = '';
        $content_html = '';
        $precioFinal = 0;
        $productVisibility = '';
        $precioFinal = (double) $product->precio;
        $precioInicial = (double) ($precioFinal / (1 + ($tienda->porcentaje_impuestos / 100)));
        $impuesto = (double) ($precioFinal - $precioInicial);
        $variantesAdapter = new Data_ProductosVariantes();
        $tagsProdsAdapter = new Data_ProductosVsTags();
        $width = '';
        $height = '';
        $margin = '';

        if ($product == false)
        {
            $infoProduct = 'No hay informaci&#243;n disponible del producto';
        }
        else
        {
            //Verifica el precio de lista del producto
            $precioDeLista = '';
            if (!is_null($product->precio_listado) && ($product->precio_listado > 0))
            {
                $precioDeLista = '      <tr>
                                            <td style="text-align: left;" width="45%"> Precio de Lista: &#160; </td>
                                            <td style="text-align: left;"> <strong><s> $' . number_format($product->precio_listado, 2) . ' </s> ' . self::currency($tienda->moneda) . '</strong> </td>
                                        </tr>';
            }

            //Verifica el tiempo de envio del producto
            $disponibilidad = '';
            if (!is_null($product->disponibilidad) && ($product->disponibilidad != ''))
            {
                $disponibilidad = '     <tr>
                                            <td style="text-align: left;" width="45%"> Tiempo de env&#237;o: </td>
                                            <td style="text-align: left;"> ' . $product->disponibilidad . ' </td>
                                        </tr>';
            }

            //Verifica si el catalogo al que pertenece el producto sea de venta
            $esDeVenta = false;
            if ($catalogo != false)
            {
                $esDeVenta = $catalogo['venta'] == 0 ? false : true;
            }

            //En caso de que exista la tienda, verificar si esta habilitada
            //en los valores de paypal estan dados de alta
            $tiendaConfig = '';
            $paymentForm = new Helper_PaymentForm($tienda);
            if (!$paymentForm->checkConfigPaymentForm()
            )
                ;
            $tiendaConfig = $paymentForm->getMessage();
            //Mensaje de producto no visible
            if ($product->orderStatus == 0 && $edicion == true)
            {
                $productVisibility .= '<tr>';
                $productVisibility .= '<td colspan="3">';
                $productVisibility .= '<div style="text-align: center; background: none repeat scroll 0 0 #FFF6BF; ">Producto oculto</div>';
                $productVisibility .= '</td>';
                $productVisibility .= '</tr>';
            }

            $agotado = self::checkIfAgoted($product, 'agotadoProd', $product->id);
            $agotado = (strlen($agotado) <= 0) ? self::checkIfProductIsNew($product, 'nuevoProd') : $agotado;

            //Se construye la informacion del producto
            $infoProduct = '';
            $infoProduct .= '<table>';

            if ($product->oferta_especial == 1)
            {
                $infoProduct .= '<tr>
                                    <td colspan="3">
                                        <div class="ofertaEspecial">
                                            <img alt="Oferta" title="Producto con precio especial" style="vertical-align:middle;" src="' . $view->getBaseUrlPublic() . '/images/ui/ofertaGif.gif"/>
                                                Precio especial.
                                        </div>
                                    </td>
                                 </tr>';
            }

            $infoProduct .= '<tr>
                                    <td colspan="3">
                                        <span style="font-size: medium;" class="nameWrap"><strong>' . $product->nombre . '</strong></span>
                                    </td>
                                 </tr>
                                 <tr>
                                     <td colspan="3">
                                         <br/>' . View_Helper_BuildAvanzado::nameEntToNumEnt(str_replace('\\', ' ', $product->descripcion)) . /* . html_entity_decode(str_replace('\\', ' ', $product->descripcion), ENT_COMPAT, 'UTF-8') . */
                    '<hr/>
                                     </td>
                                 </tr>';
            if ($product->precio > 0)
            {
                if (($tienda != false) && $tienda->impuestos == 1)
                {
                    $infoProduct .= '<tr>
                                        <td style="text-align: left;" width="45%"> Precio:</td>
                                        <td style="text-align: left;"><div class="price"><strong> $' . number_format($precioInicial, 2) . ' ' . self::currency($tienda->moneda) . '</strong></div></td>
                                     </tr>
                                     <tr>
                                        <td style="text-align: left;"> Impuestos:</td>
                                        <td style="text-align: left; "> <strong> $' . number_format($impuesto, 2) . ' ' . self::currency($tienda->moneda) . '</strong>  &#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; (' . $tienda->porcentaje_impuestos . ' %) </td>
                                     </tr>';
                }

                $infoProduct .='<tr>
                                         <td style="text-align: left;" width="45%"> Precio : &#160; </td>
                                         <td style="text-align: left;"> <strong> $' . number_format($product->precio, 2) . ' ' . self::currency($tienda->moneda) . '</strong> </td>
                                     </tr>';

                $infoProduct .=$precioDeLista;
            }

            $infoProduct .= '<tr>
                                     <td style="text-align: left;" width="45%"> Cat&#225;logo: &#160; </td>
                                     <td style="text-align: left;"> ' . $catalogo['titulo'] . ' </td>
                                 </tr>
                                 <tr>
                                     <td style="text-align: left;" width="45%"> Disponible: &#160; </td>
                                     <td style="text-align: left;" > ' . ( (($product->existencias > 0) || ($product->existencias == -1)) ? 'Si' : 'No') . ' </td>
                                 </tr>';

            //Si se ha configurado un fabricante
            if (strlen($product->fabricante) > 0)
            {
                $infoProduct .= '<tr>
                                     <td style="text-align: left;" width="45%"> Fabricante </td>
                                     <td style="text-align: left;"> ' . $product->fabricante . ' </td>
                                 </tr>';
            }

            //Si se ha configurado un sku
            if (strlen($product->fabricante) > 0)
            {
                $infoProduct .= '<tr>
                                     <td style="text-align: left;" width="45%"> Clave / SKU </td>
                                     <td style="text-align: left;"> ' . $product->sku . ' </td>
                                 </tr>';
            }

            //Si se ha configurado un peso
            if (strlen($product->fabricante) > 0)
            {
                $infoProduct .= '<tr>
                                     <td style="text-align: left;" width="45%"> Peso </td>
                                     <td style="text-align: left;"> ' . $product->peso . ' </td>
                                 </tr>';
            }

            //Si se ha configurado garantia
            if ($product->garantia > 0)
            {
                $infoProduct .= '<tr>
                                     <td style="text-align: left;" width="45%"> Garant&#237;a </td>
                                     <td style="text-align: left;"> ' . $product->garantia . ' meses</td>
                                 </tr>';
            }

            $infoProduct .= $disponibilidad;

            //Variantes de los productos
            if ($product->uso_variantes && $variantes)
            {
                $vars = '<tr>';
                $vars .= '<td>Variantes del producto</td>';
                $vars .= '<td><select id="variantes_' . $idProducto . '" name="my-item-variant-id">';
                foreach ($variantes as $variante)
                {
                    if ($variante->existencias != 0)
                        $vars .= '<option value="' . $variante->id . '">' . $variante->descripcion . '</option>';
                }
                $vars .= '</select></td>';
                $vars .= '</tr>';

                $infoProduct .= $vars;
            }

            //Se obtienen las etiquetas
            $tagsProduct = $tagsProdsAdapter->getTagsByProduct($product->id);

            $nTags = count($tagsProduct);
            //$haveTags = $nTags>0?'Etiquetas del producto => ':'';
            $comaCount = 1;

//            $stringTags = '<tr>';
//            $stringTags .= '<td>Etiquetas del producto</td>';
//            $stringTags .= '<td>';

            foreach ($tagsProduct as $key => $tag)
            {
                $haveTags .= '<span>' . $tag->valor;
                if ($nTags > 0 && $comaCount < $nTags)
                {
                    $haveTags.=', ';
                }
                $haveTags .= '</span>';
                $comaCount++;
            }
//            $stringTags .= $haveTags.'</td></tr>';
//
//            $infoProduct .= $stringTags;

            $infoProduct .= '</table>';
            $infoProduct .= '<input type="hidden" class="key" id="p-' . $idProducto . '" />';
        }

        //Obteniendo la direccion de la imagen inicial
        $pralImgReal = (($pralImg != false) ? $pralImg->ruta : '<img alt="*" src="' . $view->getBaseUrlPublic() . '/FixImages/noImage/0/' . $tienda->id_sitio . '"/>');
        $isNoImage = ($pralImg != false) ? false : true;

        //Crea el id de la imagen principal
        $idImgPral = 'imgPral_' . $elementoId . '_' . (($pralImg != false) ? $pralImg->id : 0);

        //Deshabilita o no los botones de Ver/Agregar al Carrito
        $disabledCart = ($edicion === true || $preview === true) ? 'disabled="disabled"' : '';

        $server = (PRODUCCION == 0) ? 'http://localhost/' : 'http://www.storesfactory.com/';

        //Obteniendo las rutas de la imagen thumb y la imagen real
        $rutaImagenGrande = $pralImgReal;
        $i = strpos($pralImgReal, 'src="');
        $rutaImagenGrande = str_replace(substr($pralImgReal, 0, $i + 6), '', $pralImgReal);
        $rutaImagenGrande = str_replace('"/>', '', $rutaImagenGrande);
        $rutaImagenGrande = $server . $rutaImagenGrande;

        $rutaImagenChica = $pralImghtml;
        $i = strpos($pralImghtml, 'src="');
        $rutaImagenChica = str_replace(substr($pralImghtml, 0, $i + 6), '', $pralImghtml);
        $rutaImagenChica = str_replace('"/>', '', $rutaImagenChica);
        $rutaImagenChica = $server . $rutaImagenChica;

        $dataImage = getimagesize($rutaImagenGrande);
        // Si la imagen es mas pequeña de las dimensiones aceptadas
        if ($dataImage[0] < 250 && $dataImage[1] < 250)
        {
            $width = $dataImage[0];
            $height = $dataImage[1];
            $margin = '0 auto';
        }

        //La imagen principal: Si es en edicion: Se muestra el Thumb, sino, la original, para darle el efecto de zoom
        $content_html = '<div class="productoOferta elementContent">';
        $content_html .= '<table class="item">';
        $content_html .= '<tr>';
        $content_html .= '<td id="tdImgPrincipal" width="30%" style="">';
        $content_html .= '<div class="' . $agotado . '" ></div>';
        $content_html .= '<div class="span-7">';
        $content_html .= '<div id="' . $idImgPral . '" class="listCatImgPadFix" style="width:' . $width . 'px; height:' . $height . 'px; margin:' . $margin . ';">';
        if ($edicion == false):
            $content_html .= '<a rel="position: \'inside\' , showTitle: false, adjustX:' . $zoomAdjustX . ', adjustY:' . $zoomAdjustY . '" class="cloud-zoom" href="' . $rutaImagenGrande . '" style="position: relative;max-width: 250px !important;">';
        endif;
        $content_html .= '<img alt="" title="" src="' . $rutaImagenGrande . '" style="max-height: 250px; max-width: 250px;" />';
        if ($edicion == false):
            $content_html .= '</a>';
        endif;
        $content_html .= '<span id="' . $idImgPral . '_bigImage" style="display: none;">' . $rutaImagenGrande . '</span>';
        $content_html .= '<span id="' . $idImgPral . '_smallImage" style="display: none;">' . $rutaImagenChica . '</span>';
        $content_html .= '<span id="' . $idImgPral . '_options" style="display: none;">' . $view->getBaseUrl() . ' ' . $product->id . ' ' . $tienda->id_sitio . ' ' . (($pralImg != false) ? $pralImg->index_imagen : 0) . '</span>';
        $content_html .= '</div>';
        $content_html .= '</div>';

        if (!$isNoImage)
        {
            $content_html .= '<div width="100%" style="text-align: left;" id="' . $idImgPral . '_lightBox">';
            $content_html .= '&nbsp; <a href="' . $rutaImagenGrande . '" rel="sexylightbox" style="display:block;" ><img alt="bigImg" style="max-width: 116px; max-height: 20px;" src="' . $view->getBaseUrl() . '/images/ui/btn_ampliarImagen.png" /></a></div>';
            $content_html .= '</div>';
        }

        $content_html .= '</td>';
        $content_html .= '<td>&#160;&#160;</td>';
        $content_html .= '<td style="vertical-align:middle;">' . $infoProduct . '</td>';
        $content_html .= '</tr>';
        $content_html .= '<tr>';
        $content_html .= '<td colspan="3">';
        $content_html .= $haveTags;
        $content_html .= '</td>';
        $content_html .= '</tr>';
        $content_html .= $productVisibility;
        $content_html .= '</table>';
        $content_html .= '<div>';
        $content_html .= '<table width="100%">';
        $content_html .= '<tr>' . $cellsImg . '</tr>';
        $content_html .= '</table>';
        $content_html .= '</div>';
//      si es el template3 se agrega el boton de regresar al catalogo
//        $rutaImagenChica=$view->getbaseUrl().'/FixImages/productImage/'.$pralImg->id.'/1';
//        var_dump($rutaImagenChica);
        ($view->template == 3 && $view->tipo_template == 1) ? $content_html = $content_html . '<div>' : $content_html;
        $content_html .= '<form class="jcart" action="" method="post">
                                    <input type="hidden" name="my-item-id" value="' . $idProducto . '" />
                                    <input type="hidden" name="my-item-name" value="' . $product->nombre . '" />
                                    <input type="hidden" name="my-item-price" value="' . $product->precio . '" />
                                    <input type="hidden" name="my-item-qty" value="1" />
                                    <input type="hidden" name="my-item-img" value="' . $rutaImagenChica . '" />';

        //Solo en caso de que sea de venta, inserta los botones de ver carrito y agregar a carrito
        //o los avisos para la configuracion de la tienda
        if ($esDeVenta == true)
        {
            if (strlen($tiendaConfig) > 0)
            {
                if ($edicion === true)
                {
                    //si PayPal no esta configurado, muestra mensaje, solo en modo de edicion
                    $content_html .= $tiendaConfig;
                }
            }
            else
            {    //Si no tiene error en la tienda y es un producto de venta
                //Crea el boton de Ver Carrito solo si existe un carrito habilitado
                $commerce = new Model3_Session_Namespace('commerce');
                $cart = $commerce->cart;
                $showCartButton = ($cart && $cart->getItemCount() > 0) ? ('<input class="viewcart btn-default sin-padding btn-ver-carrito"  value=" Ver carrito " type="button" name="my-viewcart-button" value="Ver carrito" ' . $disabledCart . ' onclick="(location.href=\'' . $view->url(array('controller' => 'Display', 'action' => 'viewcart')) . $nomFix . '\');"/>') : '';

                //Solo si tiene existencias el producto, se crea el boton de agregar productos
                if (($paymentForm->havePaymentForm()) && $product->precio > 0)
                {
                    if ($product->uso_variantes == 1)
                    {
                        $variantes = $variantesAdapter->find('id_producto', $product->id);
                        $totalStocks = 0;

                        foreach ($variantes as $variante)
                        {
                            if ($variante->existencias == -1)
                            {
                                $totalStocks = -1;
                                break;
                            }
                            else
                            {
                                $totalStocks += $variante->existencias;
                            }
                        }
                        $addCartButton = (($totalStocks > 0) || ($totalStocks == -1)) ? '<input class="addcart" type="button" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" ' . $disabledCart . '/>' : '';
                    }
                    else
                    {
                        $addCartButton = (($product->existencias > 0) || ($product->existencias == -1)) ? '<input class="addcart" type="button" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" ' . $disabledCart . '/>' : '';
                    }

                    //Inserta los botones en la vista del producto
                    $content_html .= $addCartButton;
                }
                $content_html .= $showCartButton;
            }
        }

        $content_html .= '</form>';
        if ($view->template == 3 && $view->tipo_template == 1/* && $view->tienda === true */)
        {
            $content_html.='<div id="" align="right">' .
                    '<a class="btntemplate  ui-button ui-widget  ui-corner-all ui-button-text-only" style="vertical-align: middle; margin: 0px;" role="button" aria-disabled="false" href="' . $view->makeLinks()->createLink($controller, $action, $params) . '">
                            <span class="ui-button-text"> Regresar al Cat&aacute;logo</span>
                        </a>
                      </div></div>';
        }
        $content_html .= '<div style="margin-top:5px;display:none;" id="add_notice_' . $idProducto . '"></div>';
        $content_html .= '</div>';
        $content_html .= '<input id="minusWidth" type="hidden" name="width" value="' . $width . '" />
                          <input id="minusHeight" type="hidden" name="height" value="' . $height . '" />';

//        return '';
        return $content_html;
    }

    /*     * **************** */

    function buildHTMLProductClearView($product, $view, $cellsImg, $zoomAdjustX, $zoomAdjustY, $variantes, $controller = '', $action = '', $params = null)
    {
        $haveTags = '';
        $infoProduct = '';
        $content_html = '';

        $productVisibility = '';
        //$variantesAdapter = new Data_ProductosVariantes();

        $width = '';
        $height = '';
        $margin = '';

        /* Creacion de la vista del producto, cada metodo regresa partes de la tabla creada */
        $content_html = '<div class="productoOferta productOffer-sf elementContent">';
        $content_html .= '<table class="productView-sf">';
        $content_html .= $this->buildProductContent($product, $variantes, $zoomAdjustX, $zoomAdjustY, $view);
//        $content_html .= self::buildProductThumbs($product, $tienda, $cellsImg);
//        $content_html .= self::buildProductDetails($product, $view);
        $content_html .= '</table>';
        $content_html .= '</div>';
        return $content_html;
    }

    /*     * *************** */

    function buildProductContent($product, $variantes, $zoomAdjustX, $zoomAdjustY, $view)
    {
        $this->_view->getJsManager()->addJs('view/helper/element/product.js');
        $this->_view->getJsManager()->addJsVar('urlAddToCart', '"' . $this->_view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'addToCart')) . '"');

        $infoProduct = '';
        $precioFinal = 0;
        $precioFinal = (double) $product->getPrice();
        $html = '';
        $width = '';
        $height = '';
        $margin = '';

        /* Creacion de la imagen* */
        //Obteniendo la direccion de la imagen inicial
        //$pralImgReal = (($pralImg != false) ? $pralImg->ruta : '<img alt="*" src="' . $view->getBaseUrlPublic() . '/FixImages/noImage/0/' . $tienda->id_sitio . '"/>');
        //$pralImgReal = (($pralImg != false) ? $pralImg->ruta : $view->getBaseUrlPublic() . '/FixImages/noImage/0/' . $tienda->id_sitio );
//        $isNoImage = ($pralImg != false) ? false : true;
        $isNoImage = false;
        //Crea el id de la imagen principal
        //$idImgPral = 'imgPral_' . $elementoId . '_' . (($pralImg != false) ? $pralImg->id : 0);
        //$location = strpos($pralImgReal,'http://');
//        $server = '';
//        $rutaImagenGrande = $pralImgReal;
//        $ruta=($pralImg != false )?$view->getbaseUrl().'/FixImages/productImage/'.$pralImg->id.'/1':'';
//        $rutaImagenChica = ($pralImg != false ? $ruta : $view->getBaseUrlPublic() . '/FixImages/noImage/0/' . $tienda->id_sitio );
//        
//        $dataImage = @getimagesize($rutaImagenGrande);
        // Si la imagen es
        //  mas pequeña de las dimensiones aceptadas
//        if ($dataImage[0] < self::IMAGE_WIDTH && $dataImage[1] < self::IMAGE_HEIGHT)
//        {
//            $width = $dataImage[0];
//            $height = $dataImage[1];
//            $margin = '0 auto';
//        }
//        $agotado = self::checkIfAgoted($product, 'agotadoProd', $product->id);
//        $agotado = (strlen($agotado) <= 0) ? self::checkIfProductIsNew($product, 'nuevoProd') : $agotado;
//  /**/  $agotado = ($product->oferta_especial == 1 && $agotado != 'agotadoProd') ? 'offerProduct' : $agotado;
//        $contentImageHtml = '<div class="' . $agotado . '" ></div>';
//        $contentImageHtml .= '<div id="' . $idImgPral . '" style="width:' . $width . '; height:' . $height . 'px; margin:' . $margin . 'px; max-width: ' . self::IMAGE_WIDTH . 'px; ">';
//        $contentImageHtml .= '<a rel="position: \'inside\' , showTitle: false, adjustX:' . $zoomAdjustX . ', adjustY:' . $zoomAdjustY . ' ,zoomWidth:' . self::IMAGE_HEIGHT . ', zoomHeight:' . self::IMAGE_HEIGHT . '" class="cloud-zoom" href="' . $rutaImagenGrande . '" style="display:auto; position: relative; max-width: ' . self::IMAGE_WIDTH . 'px !important;">';
//        
//        if ($isNoImage == false) //si se tienenlas imagenes
//        {
//            $contentImageHtml .= '<img alt="*" src="' . $view->getBaseUrlPublic() . '/FixImages/productViewImage/' . $product->getId() . '/" />';
//        }
//        else // no hay imagen
//        {
//            $contentImageHtml .= '<img class="product-img-sf" alt="" title="" src="' . $rutaImagenGrande . '" style=" max-width: ' . self::IMAGE_WIDTH . 'px;" />';
//        }
//        $contentImageHtml .= '</a>';
//        
//        $contentImageHtml .= '<span id="' . $idImgPral . '_bigImage" style="display: none;">' . $rutaImagenGrande . '</span>';
//        $contentImageHtml .= '<span id="' . $idImgPral . '_smallImage" style="display: none;">' . $rutaImagenChica . '</span>';
//        $contentImageHtml .= '<span id="' . $idImgPral . '_options" style="display: none;">' . $view->getBaseUrl() . ' ' . $product->id . ' ' . $tienda->id_sitio . ' ' . (($pralImg != false) ? $pralImg->index_imagen : 0) . '</span>';
//        $contentImageHtml .= '</div>';
        /* finaliza la creacion de la imagen */

        /* Creacion del carrito */
        $addToCar = self::buildAddToCarIfCatalogToSale($product, $view, $width, $height);

//        $oferta = '';
//        if ($product->oferta_especial == 1)
//        {
////                $oferta .= '<img alt="Oferta" title="Producto con precio especial" style="vertical-align:middle;" src="' . $view->getBaseUrlPublic() . '/images/ui/ofertaGif.gif"/>
////                                                Precio especial.';
//            $oferta.='<tr><td colspan="2"><span class="infoProduct-sf-offer">Precio especial</span></td></tr>';
//        }

        /*         * Informacion del producto* */

        $infoProduct .= '<table class="infoProduct-sf-table">';
//        $infoProduct .= $oferta;
        if ($product->getPrice() > 0)
        {
            $addText = '';

            if (!is_null($product->getPriceList()) && ($product->getPriceList() > 0) && ($product->getPrice() < $product->getPriceList()))
            {
                $addText = ' Final';
                $infoProduct .= '<tr>
                                            <td ><span class="prodct-sf-list-price-text"> Precio de Lista: &#160; </span></td>
                                            <td ><span class="prodct-sf-list-price-ammount"><strong><s> $' . number_format($product->getPriceList(), 2) . ' </s> ' . '</strong></span></td>
                                        </tr>';
            }

            $infoProduct .='<tr class="price-sf">
                                 <td  ><span class="prodct-sf-final-price-text"> Precio' . $addText . ': &#160;</span> </td>
                                 <td   ><span class="prodct-sf-final-price-ammount"><strong> $' . number_format($product->getPrice(), 2) . '</strong> </td>
                             </tr>';
        }
        else
        {
            $infoProduct .='<tr class="price-sf">
                                 <td  ><span class="prodct-sf-final-price-text">Precio : &#160;</span></td>
                                 <td   ><span class="prodct-sf-final-price-ammount"><strong>No disponible.</strong></span></td>
                             </tr>';
        }
        
        if($product->getPriceCreditos()>0){
            $infoProduct .='<tr class="price-creditos-sf">
                                 <td  ><span class="creditosItem prodct-sf-price-creditos-text">Creditos : &#160;</span></td>
                                 <td   ><span class="creditosItem prodct-sf-final-price-ammount"><strong>'.number_format($product->getPriceCreditos(), 2).'</strong></span></td>
                             </tr>';
        }

        if ($product->getVariantsUse())
        {
            $variantes=$product->getVariants();
            //$textVariants = ($tienda->texto_variantes != '') ? $tienda->texto_variantes . ':' : 'Variantes:';
            $textVariants = 'Variantes:';
            $vars = '<tr>';
            $vars .= '<td>' . $textVariants . '</td>';
            $vars .= '<td ><select id="variantes_' . $product->getId() . '" name="my-item-variant-id">';
            foreach ($variantes as $variante)
            {
                if ($variante->getStock() != 0)
                    $vars .= '<option value="' . $variante->getId() . '">' . $variante->getDescription() . '</option>';
            }
            $vars .= '</select></td>';
            $vars .= '</tr>';

            $infoProduct .= $vars;
        }

        //Si se ha configurado un sku
        if (strlen($product->getSku()) > 0)
        {
            $infoProduct .= '<tr>
                                     <td > Clave / SKU :</td>
                                     <td  > ' . $product->getSku() . ' </td>
                                 </tr>';
        }
        $infoProduct .= '</table>';



        /*         * ********************* */

//        $lightboxView = '';
//        if (!$isNoImage)
//        {
//            $lightboxView .= '<div class="wrap-view-product-img-sf" id="' . $idImgPral . '_lightBox">';
//            $lightboxView .= '&nbsp; <a class="view-product-img-sf" href="' . $rutaImagenGrande . '" rel="sexylightbox" ><img alt="bigImg" style="width: 116px; height: 20px;" src="' . $view->getBaseUrl() . '/images/ui/btn_ampliarImagen.png" /></a></div>';
//            $lightboxView .= '</div>';
//        }

        /* Creacion del titulo */
//        $html .= '<tr>
//                            <td colspan="2">
//                                <h3>' . $product->nombre . '</h3>
//                            </td>
//                      </tr>';
        /* Creacion de la imagen y datos */
        $html .= '<tr>';
        $html.= '<td>';
        $html .= '<div class="columnImage-sf">';
//        $html.= $contentImageHtml;
        $html.='</div>';
        $html.= '</td>';
        $html.= '<td>';
//        $html .= '<h1>' . $product->getName() . '</h1>';
        $html.='<br>';
        $html .= '<div class="infoProduct-sf">';
        $html .= $infoProduct;
        $html .= '<div class="infoProduct-sf-addToCar">';
        $html .= $addToCar;
        $html .= '</div>';
        $html .= '</div>';

        //metodos de pago
//        if ($tienda->use_paypal || $tienda->use_dineromail || $tienda->use_dineromail || $tienda->use_bank || $tienda->use_personal)
//        {
//            $html .= '<div class="product-sf-shipping-ops">';
//            $html .= '<strong>M&eacute;todos de pago</strong>';
//            $html .= '<ul>';
//
//            if ($tienda->use_paypal)
//            {
//                $html .= '<li>';
//                $html .= '<a href="#" onclick="javascript:window.open(\'https://www.paypal.com/mx/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside\',\'olcwhatispaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350\');"><img  src="https://www.paypal.com/es_XC/i/logo/PayPal_mark_37x23.gif" border="0" alt="Paypal"></a>';
//                $html .= '&nbsp;&nbsp;';
//                $html .= '<img alt="Visa" src="' . $view->getBaseUrlPublic() . '/images/ui/visa.png"/>';
//                $html .= '&nbsp;&nbsp;';
//                $html .= '<img alt="Mastercard" src="' . $view->getBaseUrlPublic() . '/images/ui/mastercard.png"/>';
//            }
//
//            if ($tienda->use_dineromail)
//            {
//                $html .= '<li><img alt="Dineromail" src="' . $view->getBaseUrlPublic() . '/images/dineromail.jpg"/>';
//                $html .= '&nbsp;&nbsp;';
//                $html .= '<img alt="Visa" src="' . $view->getBaseUrlPublic() . '/images/ui/visa.png"/>';
//                $html .= '&nbsp;&nbsp;';
//                $html .= '<img alt="Mastercard" src="' . $view->getBaseUrlPublic() . '/images/ui/mastercard.png"/>';
//                $html .= '&nbsp;&nbsp;';
//                $html .= '<img alt="Mastercard" src="' . $view->getBaseUrlPublic() . '/images/ui/oxxo.gif"/>';
//                $html .= '</li>';
//            }
//
//            if ($tienda->use_bank)
//                $html .= '<li>Transferencia o dep&oacute;sito bancario</li>';
//
//            if ($tienda->use_personal)
//                $html .= '<li>Pago contra entrega</li>';
//
//            $html .= '</ul>';
//            $html .= '</div>';
//        }
        $html.= '</td>';
        $html .= '</tr>';

//        $html .= '<tr>';
//        $html .= '<td colspan="2">' . $lightboxView . '</td>';
//        $html .= '</tr>';


        return $html;
    }

    static function buildProductThumbs($product, $tienda, $cellsImg)
    {
        $html = '';
        $html .= '<tr>';
        $html .= '<td colspan="2">
                            <div class="thumbsProducts-sf">
                                ' . $cellsImg . '
                            </div>
                        </td>';
        $html .= '</tr>';

        return $html;
    }

    static function buildProductDetails($product, $view)
    {
        $hasEspecifications = false;
        $especificationsProduct = '';
        $especificationsProduct .= '<ul>';

        if (!is_null($product->disponibilidad) && ($product->disponibilidad != ''))
        {
            $hasEspecifications = true;
            $especificationsProduct .= '     <li><b>Tiempo de env&#237;o:  </b>' . $product->disponibilidad . '</li>';
        }
        //Si se ha configurado un fabricante
        if (strlen($product->fabricante) > 0)
        {
            $hasEspecifications = true;
            $especificationsProduct .= '<li><b> Fabricante: </b>' . $product->fabricante . ' </li>';
        }
        //Si se ha configurado un peso
        if (strlen($product->peso) > 0)
        {
            $hasEspecifications = true;
            $especificationsProduct .= '<li> <b> Peso:  </b>' . $product->peso . ' </li>';
        }
        //Si se ha configurado garantia
        if ($product->garantia > 0)
        {
            $hasEspecifications = true;
            $especificationsProduct .= '<li><b> Garant&#237;a </b>' . $product->garantia . ' meses</li>';
        }
        $especificationsProduct .= '</ul>';

        $especificationsProduct = ($hasEspecifications == true) ? $especificationsProduct : ' <p>Producto sin especificaciones</p> ';

        $detailsEspecifications = '<div style="display:none;" id="tabEspecifiactionsData-sf_' . $product->id . '">' . $especificationsProduct . '</div>';
        $hasVideo = false;
        $hasMap = false;

        if ($product->configuracion_video_mapa)
        {
            $xml = simplexml_load_string($product->configuracion_video_mapa);
            $hasVideo = ($xml->video->status != '0' && $xml->video->ruta != '') ? true : false;
            $hasMap = ($xml->mapa->status != '0') ? true : false;
        }
        if ($hasVideo)
            $video = View_Helper_Element_ElementVideo::build($product, $view->getBaseUrlPublic(), false, false, true);
        else
            $video = ' <p>Producto sin video</p>  ';
        if ($hasMap)
            $map = View_Helper_Element_ElementMap::build($product, $view, true);
        else
            $map = ' ';
        $detailsVideo = '<div style="display:none;" id="tabVideoData-sf_' . $product->id . '">' . $video . '</div>';
        $detailsMap = '<div style="padding:15px;display:none;" id="tabMapData-sf_' . $product->id . '">' . $map . '</div> ';

        $detailsDescription = '<div id="tabDescriptionData-sf_' . $product->id . '">' . View_Helper_BuildAvanzado::nameEntToNumEnt(str_replace('\\', ' ', $product->descripcion)) . '</div>';

        $detailsProduct = '<div >';
        $detailsProduct .= '<table class="productDetails-sf">';
        $detailsProduct .= '<tr>';
        $detailsProduct .= '<td>';
        $detailsProduct .= '<div class="tabDetailsProduct-sf" id="tabDescription-sf_' . $product->id . '" style="font-weight:bold;">Descripci&oacute;n</div>';
        $detailsProduct .= '<div class="tabDetailsProductEnd-sf tabDescription"></div>';
        $detailsProduct .= '<div class="tabDetailsProduct-sf" id="tabEspecifiactions-sf_' . $product->id . '">Especificaciones</div>';
        $detailsProduct .= '<div class="tabDetailsProductEnd-sf tabEspecifiactions"></div>';
        if ($hasVideo)
        {
            $detailsProduct .= '<div class="tabDetailsProduct-sf" id="tabVideo-sf_' . $product->id . '">Video</div>';
            $detailsProduct .= '<div class="tabDetailsProductEnd-sf tabVideo"></div>';
        }
        if ($hasMap)
        {
            $detailsProduct .= '<div class="tabDetailsProduct-sf tabMap" id="tabMap-sf_' . $product->id . '">Mapa</div>';
            $detailsProduct .= '<div class="tabDetailsProductEnd-sf tabMap"></div>';
        }
        $detailsProduct .= '</td>';
        $detailsProduct .= '</tr>';
        $detailsProduct .= '<tr>';
        $detailsProduct .= '<td class="productoDetailsData-sf" id="productoDetailsData-sf-' . $product->id . '">' . $detailsDescription . $detailsEspecifications . $detailsVideo . $detailsMap . '</td>';
        $detailsProduct .= '</tr>';
        $detailsProduct .= '</table>';
        $detailsProduct .= '</div>';

        $html = '';
        $html .= '<tr>';
        $html .= '<td colspan="2">';
        $html .= $detailsProduct;
        $html .= '</td>';
        $html .= '</tr>';

        return $html;
    }

    function buildAddToCarIfCatalogToSale($product, $view, $width, $height)
    {
        $this->_view->getJsManager()->addJsVar('sfItemId', $product->getId());
        
        $htmlAddToCar = '';

//        $htmlAddToCar .= '<form class="jcart" action="" method="post">
//                                    <input type="hidden" name="my-item-id" value="' . $product->getId() . '" />
//                                    <input type="hidden" name="my-item-name" value="' . $product->getName() . '" />
//                                    <input type="hidden" name="my-item-price" value="' . $product->getPrice() . '" />
//                                    <input type="hidden" name="my-item-qty" value="1" />';
//                                    <input type="hidden" name="my-item-img" value="' . $rutaImagenChica . '" />';
//        $tiendaConfig = '';
//        $paymentForm = new Helper_PaymentForm($tienda);
//        if (!$paymentForm->checkConfigPaymentForm()
//        )
//            ;
//        $tiendaConfig = $paymentForm->getMessage();
        //Solo en caso de que sea de venta, inserta los botones de ver carrito y agregar a carrito
        //o los avisos para la configuracion de la tienda
        //Crea el boton de Ver Carrito solo si existe un carrito habilitado
        $commerce = new Model3_Session_Namespace('commerce');
        $cart = $commerce->cart;
        $showCartButton = ($cart && $cart->getItemCount() > 0 ) ? ('<input class="viewcart btn-default sin-padding btn-ver-carrito"  value="Ver carrito." type="button" name="my-viewcart-button" value="Ver carrito" onclick="(location.href=\'' . $view->url(array('controller' => 'Store', 'action' => 'viewCart')) . '\');"/>') : '';

        //Solo si tiene existencias el producto, se crea el boton de agregar productos
        if (true || ($paymentForm->havePaymentForm()) && $product->precio > 0)
        {

            if ($product->getVariantsUse() == 1)
            {
                $totalStocks = 0;
                $variants=$product->getVariants();
                foreach ($variants as $variant)
                {
                    if ($variant->getStock() == -1)
                    {
                        $totalStocks = -1;
                        break;
                    }
                    else
                    {
                        $totalStocks += $variant->getStock();
                    }
                }
                $addCartButton = (($totalStocks > 0) || ($totalStocks == -1)) ? '<input class="addcart btn-default sin-padding btn-agregar-carrito" id="sfAddToCartButton" type="button" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" />' : '';
            }
            else
            {
                $addCartButton = (($product->getStock() > 0) || ($product->getStock() == -1)) ? '<input class="addcart btn-default sin-padding btn-agregar-carrito" id="sfAddToCartButton" type="button" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" />' : '';
            }

            //Inserta los botones en la vista del producto
            $htmlAddToCar .= $addCartButton;
        }
        $htmlAddToCar .= $showCartButton;


//        $htmlAddToCar .= '</form>';

        $htmlAddToCar .= '<div style="margin-top:5px;display:none;" id="add_notice_' . $product->getId() . '"></div>';
        $htmlAddToCar .= '<input id="minusWidth" type="hidden" name="width" value="' . $width . '" />
                                    <input id="minusHeight" type="hidden" name="height" value="' . $height . '" />';

        return $htmlAddToCar;
    }

    static function buildBotonContact($view)
    {
        $addBotonC = '';
        $addBotonC .= '<input id="urlBottonContactProduct" type="hidden" value="' . $view->getBaseUrl() . '/AjaxCart/botonContact"/>
            <input id="idSitioProduct" type="hidden" value="' . $view->idSitio . '"/>
            <div id="dialog-form-product" class="dialog-form-product"style="display:none" title="Contacto" >
                <div style="display:none" id="msg_prod"></div>
                <form id="validateContactProduct">
                <table style="color:#222;border:none">
                        <tr><td><label for="name">Nombre</label></td>
                        <td><input style="width:255px;" type="text" name="name" id="name" value=""  /></td></tr>
                        <tr><td><label for="email">Mail</label></td>
                        <td><input style="width:255px;" type="text" name="email" id="email" value="" /></td></tr>
                        <tr><td><label for="telefono">Tel&eacute;fono</label></td>
                        <td><input style="width:255px;" type="text" name="telefono" id="telefono" value=""/></td></tr>
                        <tr><td><label style="vertical-align:top;" for="comentario">Comentario</label></td>
                        <td><textarea style="width:255px;" name="comentario" id="comentario" value=""></textarea></td></tr>
                </table>
                </form>
         </div>';
        return($addBotonC);
    }

    static function checkIfAgoted($product, $class, $idProduct)
    {
        $variantesAdapter = new Data_ProductosVariantes();
        if ($product->uso_variantes == 1)
        {
            $variantes = $variantesAdapter->find('id_producto', $idProduct);
            $totalStocks = 0;
            foreach ($variantes as $variante)
            {
                if ($variante->existencias == -1)
                {
                    $totalStocks = -1;
                    break;
                }
                else
                {
                    $totalStocks += $variante->existencias;
                }
            }
            $agoted = (($totalStocks == 0)) ? $class : '';
        }
        else
        {
            $agoted = (($product->existencias == 0)) ? $class : '';
        }
        return $agoted;
    }

    static function checkIfProductIsNew($product, $class)
    {
        $classNew = '';
        $today = date('Y-m-d H:i:s');

        $startDate = $product->fecha_inicio_nuevo;
        $endDate = $product->fecha_fin_nuevo;

        if ($startDate > $endDate)
        {
            $dateAux = $startDate;
            $startDate = $endDate;
            $endDate = $dateAux;
        }
        //echo '<span style="display:none;">'.strtotime($today).'>='.strtotime($startDate).' and '.strtotime($today).'<='.strtotime($endDate).'</span>';
        if (( strtotime($today) >= strtotime($startDate)) && (strtotime($today) <= strtotime($endDate)))
        {
            $classNew = $class;
        }

        return $classNew;
    }

    static function currency($currency)
    {
        switch ($currency)
        {
            case M3Commerce_Class_SalesProcess::MXN :
                $str = M3Commerce_Class_SalesProcess::MXN_STR;
                break;
            case M3Commerce_Class_SalesProcess::USD :
                $str = M3Commerce_Class_SalesProcess::USD_STR;
                break;
            default:
                $str = M3Commerce_Class_SalesProcess::MXN_STR;
                break;
        }

        return $str;
    }

}

?>
