<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ElementCatalogo
 *
 * @author usuario
 */
class View_Helper_Element_ElementCatalog extends Model3_View_Helper
{

    private $_idSitio;
    private $_preview;
    private $_editMode;
    private $_columns;
    private $_percent;
    private $_elemento;
    private $_currentPage;
    private $_usingPaginator;
    private $_desdeTienda;
    private $_nomFix;
    private $_tienda;
    private $_rows;

    public function build($catalog, $currentPage = 1, $usingPaginator = false, $desdeTienda = false)
    {
        $this->_elemento = $catalog;
        $this->_columns = 4;
        $this->_percent = round(100 / $this->_columns);
        $this->_currentPage = $currentPage;
        $this->_usingPaginator = $usingPaginator;
        $this->_desdeTienda = $desdeTienda;
        $this->_rows = 2;

        $numProductsToShow = 8;
        $totalPages = 2;
        $esDeVenta = true;
        $strTags = '';

        if ($this->_desdeTienda === true)
        {
            $lineas = 2;
            $this->_columns = 4;
            $tienda = $tiendasAdapter->getStoreForASite($this->_idSitio);
            $this->_tienda = $tienda = (is_array($tienda)) ? ((count($tienda) > 0) ? $tienda[0] : false) : false;
            if (($tienda->lineas_catalogo) != '')
            {
                $lineas = $tienda->lineas_catalogo;
                $numProductsToShow = $lineas * $this->_columns;
            }
            else
            { //si no tiene tienda
                $numProductsToShow = $lineas * $this->_columns;
            }
        }
        else
        {
            $this->_columns = 4; //por default 4 columnas
            $numProductsToShow = $this->_columns * 2; //por default 4*2
        }
        
        return $this->buildHTMLCatalogGallery($catalog);
    }

    /**print
     * Funcion que se encarga de construir el catalogo en forma de galeria con los productos e imagenes
     * @param array $productsAndImgs arreglo con los productos e imagenes
     * @param <type> $tienda tienda del sitio
     * @param <type> $catalogo catalogo que se va a mostrar
     * @param int $totalPages Numero de paginas utilizada para la paginacion
     * @return string contenido del html
     */
    public function buildHTMLCatalogGallery($catalog)
    {
        $content_html = '';

        $content_html .= '<table class="catalogoGaleria table table-striped table-bordered table-condensed"> ';
        $groups = $this->convertProductsToGroups($catalog->getProducts());
        $gruposPorPagina = array();
        $numDePaginas = ceil(count($groups)/$this->_rows);
        for($i = 0; $i <  $this->_rows; $i += 1){
            if(count($groups) > ($this->_currentPage*$this->_rows) - ($this->_rows - $i)){
                array_push($gruposPorPagina, ($groups[($this->_currentPage*$this->_rows) - ($this->_rows - $i)]) );

            }
        } 
        
        if (is_array($gruposPorPagina) && count($gruposPorPagina) > 0)
        {
            foreach ($gruposPorPagina as $group)
            {
                $content_html .= $this->buildHTMLCatalogRowItem($group);
            }
        }
        
        $content_html .= '</table>';
        $content_html .= '<div class="pagination text-center tabla">';
        $content_html .= '<ul class="pagination">';
        if(isset($_POST['arguments'])){
            $pagActual = $_POST['arguments'];
            if($pagActual > 1){
                $irA =  $pagActual - 1 ;
                $pagAnterior = "&lt Página Anterior";
                $content_html .= '<li class="" id="pagina'.$irA.'" ><a  href="#'.$i.'" onclick="goToPage('.$irA .')">'.$pagAnterior.'</a></li>';
            } else {
                $pagAnterior = "&lt Página Anterior";
                $content_html .= '<li class="active" id="pagina'.$irA.'" style="color:gray;" ><a  >'.$pagAnterior.'</a></li>';
            }
        } else {
            $pagAnterior = "&lt Página Anterior";
            $content_html .= '<li class="active" id="pagina" style="color:gray;" ><a  >'.$pagAnterior.'</a></li>';
        }
        for($i = 1; $i <= $numDePaginas; $i += 1){
            if($this->_currentPage  == $i) 
                $content_html .= '<li class="active" id="pagina'.$i.'" ><a  onclick="">'.$i.'</a></li>';
            else
                $content_html .= '<li class="" id="pagina'.$i.'" ><a  href="#'.$i.'" onclick="goToPage('.$i.')">'.$i.'</a></li>';
        }
        
        if(isset($_POST['arguments'])){
            $pagActual = $_POST['arguments'];
            if($pagActual < $numDePaginas){
                $irA =  $pagActual + 1 ;
                $pagSiguiente = "Página Siguiente &gt";
                $content_html .= '<li class="" id="pagina'.$irA.'" ><a  href="#'.$i.'" onclick="goToPage('.$irA .')">'.$pagSiguiente.'</a></li>';
            } else if($pagActual == $numDePaginas) {
                $pagSiguiente = "Página Siguiente &gt";
                $content_html .= '<li class="active" id="pagina" style="color:gray;" ><a  >'.$pagSiguiente.'</a></li>';
            }
        } else if(!isset($_POST['arguments']) && $this->_currentPage == $numDePaginas){
            $pagSiguiente = "Página Siguiente &gt";
            $content_html .= '<li class="active" id="pagina" style="color:gray;" ><a  >'.$pagSiguiente.'</a></li>';
        } else {
            $pagSiguiente = "Página Siguiente &gt";
             $irA =  $this->_currentPage + 1 ;
            $content_html .= '<li class="" id="pagina'.$irA.'" ><a  href="#'.$i.'" onclick="goToPage('.$irA .')">'.$pagSiguiente.'</a></li>';
        }
        
        $content_html .= '</ul>';
        $content_html .= '</div>';
        return $content_html;
    }

    private function convertProductsToGroups($products)
    {
        $grupos = array();
        $i = 0;
        $j = 0;

        if (count($products) > 0)
        {
            foreach ($products as $product)
            {
                if($product->getStatus() == 1) //Si el producto no ah sido borrado
                {
                    $grupos[$j][$i++] = $product;
                    if (($i % $this->_columns) == 0)
                    {
                        $j++;
                        $i = 0;
                    }
                }
            }
        }
        return $grupos;
    }

    private function buildHTMLCatalogRowItem($products)
    {
        //borrar esto y documentar
        //$tot solo sirve para saber la cantidad que tendra por si el numero es menor crear tr y td's vacios
        //recibe productos en grupos de 4
        $html = '';
        $html .= '<tr>';
        if (is_array($products) && count($products) > 0)
        {
            foreach ($products as $k => $product)
            {
                $html .= '<td width="' . $this->_percent . '%" class="catItem" >';
//                $html .= $this->buildHTMLCatalogGalleryItem($product);
                $html .= $this->buildHTMLCatalogGalleryItem($product);
                $html .= '</td>';
                if ($k < $this->_columns - 1)
                {
                    $html .= '<td class="verticalSeparator" rowspan="2" >';
                    $html .= '</td>';
                }
            }
        }
        for ($i = $k + 1; $i < $this->_columns; $i++)
        {
            $html .= $this->buildHTMLCatalogGalleryItemEmpty();
            if ($i < $this->_columns - 1)
            {
                $html .= '<td class="verticalSeparator" rowspan="2" >';
                $html .= '</td>';
            }
        }
        $html .= '</tr>';
        /* Separator */
        $html .= '<tr>';
//        $html .= '<td colspan="' . ($this->_columns * 2 - 1) . '" class="horizontalSeparator">';
//        $html .= '<hr/>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }

    private function buildHTMLCatalogGalleryItem($product)
    {
        $infoProduct = '';
        $fixHelper = new Helper_FixHelper();
        $imgHTML = '';
        $location = (!isset($product->ruta)) ? false : strpos($product->ruta, 'http://');
        $images = $product->getImages();
        $firstImage = false;
        foreach($images as $i)
        {
            $firstImage = $i;
            break;
        }

        if ($firstImage instanceof DefaultDb_Entities_ProductImages)
        {
            $imgHTML = $this->_view->ManagerImages()->thumbImg($firstImage->getPath(),$product->getName(),$product->getName(),'class="thumbnail"','200','200');
        }
        else
            $imgHTML = $this->_view->ManagerImages()->thumbImg('images/products/default.jpg','Producto','Producto','class="thumbnail"','200','200');

        $url = $this->_view->url(array('controller' => 'Store', 'action' => 'viewProduct', 'id' => $product->getId()));
//        $agotado = View_Helper_Element_ElementProducto::checkIfAgoted($product, 'emptyCat', $product->getId());
//        $agotado = (strlen($agotado) <= 0) ? View_Helper_Element_ElementProducto::checkIfProductIsNew($product, 'newProdCat') : $agotado;
//        $agotado = ($product->oferta_especial == 1 && $agotado != 'emptyCat') ? 'offerCatalog' : $agotado;

        
        $infoProduct .= '<div class="contentItem">';
//        $infoProduct .= '<div class="' . $agotado . '"></div>';
        $infoProduct .= '<a href="' . $url . '">';
        $infoProduct .= '<img class="thumbnail" src="/public/'.($firstImage ? $firstImage->getPath() : "").'" alt="'.$product->getName().'" title="'.$product->getDescription().'" >';                //$infoProduct .=$imgHTML;
        $infoProduct .= '</a>';
        $infoProduct .= '</div>';        
        //$infoProduct .='</div>';
        
        $functionShowProduct = ($this->_editMode == false && $this->_preview == false) ? 'onclick="itemSelectedFromCatalog(\'' . $fixHelper->generateFix($product->getName()) . '\', ' . $product->getId() . ', \'' . $this->_view->url(array("controller" => 'Store', 'action' => 'viewProduct')) . '\');"' : '';
        $showPointerMouse = ($this->_editMode == false) ? 'onmouseover="this.style.cursor=\'pointer\';"' : '';
        $infoProduct .='<span class="nameItem" ' . $showPointerMouse . ' ' . $functionShowProduct . ' >' . $product->getName() . '</span>';
        $infoProduct .='<br/>';
        $infoProduct .='Precio : <span class="costItem"> $' . number_format($product->getPrice(), 2) . '</span>';
        if($product->getPriceCreditos()>0){
            $infoProduct .='<br/>';
            $infoProduct .=' <span class="creditosItem"> Creditos :<strong>' . number_format($product->getPriceCreditos(), 2) . '</strong></span>';
        }
        
        return $infoProduct;
    }

    private function buildHTMLCatalogGalleryItemEmpty()
    {
        $infoProduct = '';
        $infoProduct .= '<td class="catItem" width="' . $this->_percent . '%">';
        $infoProduct .= '<div class="emptyDiv"></div>';
        $infoProduct .='</td>';
        return $infoProduct;
    }    

    /* Temporal para que funcione el elemento galeria item */

    /**
     * Crea uno de los productos con tipo de vista galeria.
     * @param <type> $product
     * @param <type> $view
     * @param <type> $idElemento
     * @param <type> $tienda
     * @return string               Cadena de HTML del producto en forma de galeria
     */
    static function buildHTMLCatalogoTipoGaleriaItem($product, $view, $idElemento, $tienda)
    {
        $infoProduct = '';
        $strPrice = '';
        if ($product->precio > 0)
        {
            $precioFinal = (double) $product->precio;
            if ($tienda)
            {
                $precioInicial = (double) ($precioFinal / (1 + ($tienda->porcentaje_impuestos / 100)));
            }
            else
            {
                $precioInicial = $precioFinal;
            }
            $impuesto = (double) ($precioFinal - $precioInicial);
            $strPrice = ' $' . number_format($precioFinal, 2) . ' ' . self::currency($tienda->moneda);
        }
        else
        {
            $strPrice = 'No disponible';
        }
        $agotado = View_Helper_Element_ElementProducto::checkIfAgoted($product, 'agotadoCat', $product->id);
        $agotado = (strlen($agotado) <= 0) ? View_Helper_Element_ElementProducto::checkIfProductIsNew($product, 'nuevoProdCat') : $agotado;
//        $agotado = ($product->oferta_especial == 1) ? 'offerProduct': $agotado;

        $imgHTML = '';
        if (is_null($product->ruta) || $product->ruta == '')
        {
            $imgHTML = '<img alt="' . $product->nombre . '" src="' . $view->getBaseUrlPublic() . '/FixImages/noImage/2/' . $tienda->id_sitio . '">';
        }
        else
        {
            $imgHTML = '<img alt="' . $product->nombre . '" src="' . $view->getBaseUrl() . '/FixImages/imagesGalleryCatalog/1/' . $product->id . '/' . $tienda->id_sitio . '"/>';
        }

        $infoProduct = '<div class="' . $agotado . '"></div>';
        $infoProduct .= '<div class="imgWrapper">';
        $infoProduct .= '<div class="sectionCatalogImg" id="imgGaleria_' . $idElemento . '_' . $product->id . '">';
        $infoProduct .= $imgHTML;
        $infoProduct .= '</div>';
        $infoProduct .= '<div class="sectionCatalogText">';
        $infoProduct .= '<div class="product-sf-name" id="nombreGaleria_' . $idElemento . '_' . $product->id . '">';
        $infoProduct .= '<label>' . $product->nombre . '</label>';
        $infoProduct .= '</div>';

        $infoProduct .= '<div id="precioGaleria_' . $idElemento . '_' . $product->id . '">';
        $infoProduct .= '<span class="prodct-sf-final-price-ammount">';
        $infoProduct .= '<strong>' . $strPrice . '</strong>';
        $infoProduct .= '</span>';
        $infoProduct .= '</div>';

        if ($product->orderStatus == 0)
        {
            $infoProduct .= '<div class="product-sf-not-showed">Producto oculto</div>';
        }
        $infoProduct .= '</div>';

        return $infoProduct;
    }

    public function buildHTMLCatalogList($productsAndImgs, $tienda, $catalogo, $totalPages)
    {
        $content_html = "";
        if ($totalPages > 1)
        {
            $content_html .= '<center>';
            $content_html .= $this->BarraDePaginacion($this->_elemento['id'], $totalPages, $this->_currentPage, $this->_idSitio, 2, $catalogo['id'], $this->_editMode, $this->_preview, $this->_desdeTienda);
            $content_html .= '</center>';
        }
        $content_html .= '<table class="catalogoGaleria"> ';
        //var_dump($productsAndImgs);
        if (is_array($productsAndImgs) && count($productsAndImgs) > 0)
        {
            foreach ($productsAndImgs as $product)
            {
                $content_html .= $this->buildHTMLCatalogListItem($product, $catalogo, $tienda);
            }
            $content_html .= $this->buildBotonContact();
        }
        $content_html .= '</table>';
        if ($totalPages > 1)
        {
            $content_html .= '<center>';
            $content_html .= $this->BarraDePaginacion($this->_elemento['id'], $totalPages, $this->_currentPage, $this->_idSitio, 2, $catalogo['id'], $this->_editMode, $this->_preview, $this->_desdeTienda);
            $content_html .= '</center>';
        }
        return $content_html;
    }

    public function buildHTMLCatalogListItem($product, $catalogo, $tienda)
    {
        $infoProduct = "";
        $imgHTML = '';
        if (is_null($product->ruta) || $product->ruta == '')
        {
            $imgHTML = '<img alt="' . $product->nombre . '" src="' . $this->_baseUrl . '/FixImages/noImage/1/' . $tienda->id_sitio . '" style="padding:10px 0 0 0;"/>';
        }
        else
        {
            $imgHTML = '<img alt="' . $product->nombre . '" src="' . $this->_baseUrl . '/FixImages/imagesGalleryCatalog/0/' . $product->id . '/' . $tienda->id_sitio . '"/ style="padding:10px 0 0 0;"/>';
        }
        $fixHelper = new Helper_FixHelper();
        $functionShowProduct = ($this->_editMode == false && $this->_preview == false) ? 'onclick="itemSelectedFromCatalog(\'' . $fixHelper->generateFix($product->nombre) . '\', ' . $product->id . ', \'' . $this->getInithPath($this->_idSitio, $this->_baseUrl) . '\');"' : '';
        $showPointerMouse = ($this->_editMode == false) ? 'onmouseover="this.style.cursor=\'pointer\';"' : '';

        $agotado = View_Helper_Element_ElementProducto::checkIfAgoted($product, 'emptyCat', $product->id);
        $agotado = (strlen($agotado) <= 0) ? View_Helper_Element_ElementProducto::checkIfProductIsNew($product, 'newProdCat') : $agotado;
        //  var_dump($agotado);
        $agotado = ($product->oferta_especial == 1 && $agotado != 'emptyCat') ? 'offerCatalog' : $agotado;
        //    var_dump($agotado);
        $infoProduct .= '<tr style=height:1px;>';
        $infoProduct .= '<td rowspan="5" class="imageItem" ' . $showPointerMouse . ' ' . $functionShowProduct . '>';
        $infoProduct .= '<div class="contentItem">';
        $infoProduct .= '<div class="' . $agotado . '"></div>';
        $infoProduct .=$imgHTML;
        $infoProduct .= '</div>';
        $infoProduct .= '</td>';

        //Separator vertical
        $infoProduct .= '<td rowspan="5" class="verticalSeparator">';
        $infoProduct .= '</td>';
        $infoProduct .= '</tr>';

        //nombre del producto
        $infoProduct .= '<tr>';
        $infoProduct .= '<td class="descriptionItem" valign="top">';
        $infoProduct .= '<span class="nameProduct" ' . $showPointerMouse . ' ' . $functionShowProduct . '>' . $product->nombre . '</span>';
        $infoProduct .= '</td>';
        $infoProduct .= '</tr>';

        //precios y agregar a carrito
        //var_dump($product);
        $infoProduct .= '<tr>';
        $infoProduct .= '<td class="descriptionItem"  >';
        /**/ if (!is_null($product->precio_listado) && ($product->precio_listado > 0) && ($product->precio < $product->precio_listado))
        {
            $infoProduct .='Precio lista:<span class="costItem"> $' . number_format($product->precio_listado, 2) . ' ' . $this->currency($tienda->moneda) . '</span><br/>';
        }
        $stringPrice = ( $product->precio > 0 ) ? '$' . number_format($product->precio, 2) : 'No disponible';
        $infoProduct .='Precio : <span class="costItem"> ' . $stringPrice . ' ' . $this->currency($tienda->moneda) . '</span>';
        //falta haver pruebas para saber si puede agregar al carrito si puede vender etc.
        if ($product->destacado == 0 && ( $catalogo['venta'] == 0 || $product->precio <= 0 ))
        {
            //var_dump($product->id);
            $infoProduct .= '</td></tr>';
            $infoProduct .= '<tr><td>';
            $infoProduct .= '<input class="addcart addcartCatalog contact_b" style="text-align: center" name="my-contact-button" value="Contacto"/>';
            $infoProduct .= '</td></tr>';
        }
        else
            $infoProduct .= $this->buildAddToCart($product, $tienda);

        $infoProduct .= '</td>';
        $infoProduct .= '</tr>';

        //Descripcion del producto
        $shortDescription = $product->descripcion;
        $shortDescription = strip_tags($shortDescription);
        if (strlen($shortDescription) > 200)
        {
            $shortDescription = substr($shortDescription, 0, 200);
        }
        $infoProduct .= '<tr>';
        $infoProduct .= '<td class="descriptionItemShort">';
        $infoProduct .='<font >' . $shortDescription . ' </font><br/>';
        $infoProduct .= '<span ' . $showPointerMouse . ' ' . $functionShowProduct . ' ><img src="' . $this->_baseUrl . '/images/go.png"/>más info</span>';
        $infoProduct .= '</td>';
        $infoProduct .= '</tr>';

        //separator horizontal
        $infoProduct .= '<tr>';
        $infoProduct .='<td colspan="2" class="horizontalSeparator">';
        $infoProduct .= '</td>';
        $infoProduct .= '</tr>';

        return $infoProduct;
    }

    public function buildBotonContact()
    {
        $addBotonC = '';
        $addBotonC .= '<tr><td>
            <input id="urlBottonContact" type="hidden" value="' . $this->_baseUrl . '/AjaxCart/botonContact"/>
            <input id="idSitio" type="hidden" value="' . $this->_idSitio . '"/>
            <div id="dialog-form" style="display:none" title="Contacto" >
                <div style="display:none" id="msg"></div>
                <table style="color:#222">
                <form id="validateContact">
                        <tr><td><label for="name">Nombre</label></td>
                        <td><input style="width:255px" type="text" name="name" id="name" value=""  /></td><td></td></tr>
                        <tr><td><label for="email">Mail</label></td>
                        <td><input style="width:255px" type="text" name="email" id="email" value="" /></td><td></td></tr>
                        <tr><td><label for="telefono">Tel&eacute;fono</label></td>
                        <td><input style="width:255px" type="text" name="telefono" id="telefono" value=""/></td><td></td></tr>
                        <tr><td colspan="2"><label style="vertical-align:top" for="comentario">Comentario</label></td></tr>
                        <tr><td></td><td><textarea style="width:255px" name="comentario" id="comentario" value=""></textarea></td><td></td></tr>
                </form>
                </table>
         </div></td></tr>';
        return($addBotonC);
    }

    public function buildAddToCart($product, $tienda)
    {
        $rutaImagenChica = '';
        //var_dump($product);
        if (isset($product->ruta) && $product->ruta != '')
        {
            //$rutaImagenChica = explode('"', $product->thumb);
            //$rutaImagenChica = $rutaImagenChica[3];
            //$rutaImagenChica = $product->thumb;
            $imagenesAdapter = new Data_ImagenesProductos();
            $productoImg = $imagenesAdapter->findOne('id_producto', $product->id);
            $ruta = $this->_baseUrl . '/FixImages/productImage/' . $productoImg->id . '/1';
            $rutaImagenChica = $ruta;
        }
        else
            $rutaImagenChica = $this->_baseUrl . '/FixImages/noImage/0/' . $tienda->id_sitio;
        $disabledCart = ($this->_editMode === true || $this->_preview === true) ? 'disabled="disabled"' : '';
        $addCart = '';
        $addCart .= '<form class="jcart" action="" method="post">
                         <input type="hidden" name="my-item-id" value="' . $product->id . '" />
                         <input type="hidden" name="my-item-name" value="' . $product->nombre . '" />
                         <input type="hidden" name="my-item-price" value="' . $product->precio . '" />
                         <input type="hidden" name="my-item-qty" value="1" />
                        <input type="hidden" name="my-item-img" value="' . $rutaImagenChica . '" />';

        $tiendaConfig = '';
        $paymentForm = new Helper_PaymentForm($tienda);
        if (!$paymentForm->checkConfigPaymentForm())
            $tiendaConfig = $paymentForm->getMessage();
        if (strlen($tiendaConfig) > 0)
        {
            if ($this->_editMode === true)
            {
                //si PayPal no esta configurado, muestra mensaje, solo en modo de edicion
                $addCart .= $tiendaConfig;
            }
        }
        else
        {
            $addCartButton = '';
            if (($paymentForm->havePaymentForm()) && $product->precio > 0)
            {
                if ($product->uso_variantes == 1)
                {
                    $variantesAdapter = new Data_ProductosVariantes();
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
                    $addCartButton = (($totalStocks > 0) || ($totalStocks == -1)) ? '<input class="addcart addcartCatalog" type="submit" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" ' . $disabledCart . '/>' : '';
                }
                else
                {
                    $addCartButton = (($product->existencias > 0) || ($product->existencias == -1)) ? '<input class="addcart addcartCatalog" type="submit" title="Agregar al carrito." name="my-add-button" value="Agregar al carrito" ' . $disabledCart . '/>' : '';
                }
            }

            $viewCartButton = '';
            $commerce = new Model3_Session_Namespace('commerce');
            $cart = $commerce->cart;
            if ($cart && $cart->getItemCount() > 0)
            {
                if (PRODUCCION == 0)
                    $viewCartButton = '<input type="button" class="viewcart viewcartCatalog" name="my-viewcart-button" value="Ver carrito"  ' . $disabledCart . ' onclick="location.href=\'' . $this->_baseUrl . '/Display/viewcart/' . $this->_nomFix . '\'">';
                else
                    $viewCartButton = '<input type="button" class="viewcart viewcartCatalog" name="my-viewcart-button" value="Ver carrito"  ' . $disabledCart . ' onclick="location.href=\'' . $this->_baseUrl . '/viewcart\'">';
            }
            $addCart .= '<div style="display:block; width:auto; overflow:hidden;">';
            $addCart .=$addCartButton;
            $addCart .=$viewCartButton;
            $addCart .= '</div>';
        }
        $addCart .='</form>';
        $addCart .= '<div class="cartNotice" id="add_notice_' . $product->id . '"></div>';
        $addCart .= '<div style="float:right; margin-top:5px;display:none;" id="add_notice_' . $product->id . '"></div>';
        return $addCart;
    }

    public function buildHTMLCatalogoTipoProductos($productsAndImgs, $view, $idElemento, $idSitio, $editMode, $tienda, $esDeVenta, $nomFix, $catalogo, $totalPages, $currentPage, $preview, $desdeTienda)
    {
        $content_html = '';
        $functionShowProduct = '';
        $cellsImg = '';
        //$tienda = false;
        $idProducto = 0;
        $product = false;
        $pralImg = false;
        $catalog = false;
        $pralImghtml = '';
        $zoomAdjustX = 0;
        $zoomAdjustY = 0;
        $variantes = false;

        if (($productsAndImgs == false) || (is_array($productsAndImgs) && count($productsAndImgs) <= 0))
        {
//            $content_html = '<div class="notice" style="text-align: center"> No hay informaci&#243;n disponible de los productos </div>';
            $content_html = '';
        }
        else
        {
            //Verificando que la tienda del sitio este configurada para payPal
            //Para las vistas de ejemplos, la variable $tienda llegara con true
            $tiendaConfig = '';
            if ($esDeVenta === true)
            {
                $paymentForm = new Helper_PaymentForm($tienda);
                if (!$paymentForm->checkConfigPaymentForm())
                    ;
                $tiendaConfig = $paymentForm->getMessage();
            }

            $espacios = '&#160;&#160;&#160;';
            $celdaEspacios = '<td>' . $espacios . '</td>';
            $renglonEspacios = '<tr><td>' . $celdaEspacios . '</td></tr>';

            $content_html .= '<table class="catalogoLista">';
            $content_html .= '<th><h3><strong>' . (($catalogo != false) ? $catalogo['titulo'] : '') . '</strong></h3></th>';
            $content_html .= ( (strlen($tiendaConfig) > 0) && $editMode) ? '<tr><td>' . $tiendaConfig . '</td></tr>' : '';

            if ($totalPages > 1)
            {
                $content_html .= '<tr class="paginator_container">';
                $content_html .= '  <td>';
                $content_html .= '      <center>';
                $content_html .= $this->BarraDePaginacion($idElemento, $totalPages, $currentPage, $idSitio, 3, $catalogo['id'], $editMode, $preview, $desdeTienda);
                $content_html .= '      </center>';
                $content_html .= '  </td>';
                $content_html .= '</tr>';
            }

            foreach ($productsAndImgs as $product)
            {
                if (($editMode == true) || ($product->orderStatus && $preview == true) || ($product->orderStatus && !$editMode && !$preview))
                {
                    $content_html .= '<tr>';
                    $content_html .= '  <td  class="item"  id="itemListaProductos_' . $idElemento . '_' . $product->id . '" >';

                    View_Helper_Element_ElementProducto::prepareInfoProduct($product->id, $view, $idSitio, $idElemento, $editMode, $product, $catalog, $pralImg, $pralImghtml, $cellsImg, $tienda, $zoomAdjustX, $zoomAdjustY, $variantes);
                    $content_html .= View_Helper_Element_ElementProducto::buildHTMLProductClearView($product, $view, $pralImg, $pralImghtml, $cellsImg, $product->id, $idElemento, $editMode, $nomFix, $catalog, $tienda, $preview, $zoomAdjustX, $zoomAdjustY, $variantes);
                    $content_html .= '<input type="hidden" id="p-' . $product->id . '" class="key"/>';
                    $content_html .= '  </td>';
                    $content_html .= '</tr>';

                    $content_html .= $renglonEspacios;
                }
            }

            if ($totalPages > 1)
            {
                $content_html .= '<tr class="paginator_container">';
                $content_html .= '  <td>';
                $content_html .= '      <center>';
                $content_html .= $this->BarraDePaginacion($idElemento, $totalPages, $currentPage, $idSitio, 3, $catalogo['id'], $editMode, $preview, $desdeTienda);
                $content_html .= '      </center>';
                $content_html .= '  </td>';
                $content_html .= '</tr>';
            }

            $content_html .= '</table>';
        }

        $content_html .= '<input type="hidden" id="adjustX" value="' . $zoomAdjustX . '"/>';
        $content_html .= '<input type="hidden" id="adjustY" value="' . $zoomAdjustY . '"/>';
        return $content_html;
    }

    public function BarraDePaginacion($idElemento, $numPages, $currentPage, $idSitio, $tipoVista, $idCatalogo, $editMode, $previewMode, $desdeTienda)
    {
        $editMode = ($editMode == true) ? 1 : 0;
        $previewMode = ($previewMode == true) ? 1 : 0;
        $paginacionHTML = '';

        //Si de la pagina actual hay aun una pagina anterior, muestra link a la pagina anterior
        if (($currentPage - 1) > 0)
        {
            $paginacionHTML .= '<a class="prevNext" title="Anterior" onclick="changePagCatalog(\'' . $this->_baseUrl . '/AjaxChangePagCatalog/change' . '\', ' . $idElemento . ', ' . ($currentPage - 1) . ', ' . $idSitio . ', ' . $tipoVista . ', ' . $idCatalogo . ', ' . $editMode . ', ' . $previewMode . ', ' . (($desdeTienda == false) ? 0 : 1) . ');"> &lt;&lt; </a>';
        }

        //Imprime la secuencia de paginas
        if ($numPages > 1)
        {
            for ($numPag = 1; $numPag <= $numPages; $numPag++)
            {
                if ($currentPage == $numPag)
                {
                    $paginacionHTML .= '<strong>' . $currentPage . '</strong>';
                }
                else
                {
                    $paginacionHTML .= '<a class="prevNext" onclick="changePagCatalog(\'' . $this->_baseUrl . '/AjaxChangePagCatalog/change' . '\', ' . $idElemento . ', ' . $numPag . ', ' . $idSitio . ', ' . $tipoVista . ', ' . $idCatalogo . ', ' . $editMode . ', ' . $previewMode . ', ' . (($desdeTienda == false) ? 0 : 1) . ');" style=""> ' . $numPag . ' </a>';
                }
            }
        }

        //Si de la pagina actual hay aun una pagina siguiente, muestra link a la pagina siguiente
        if (($currentPage + 1) <= $numPages)
        {
            $paginacionHTML .= '<a class="prevNext" title="Siguiente" onclick="changePagCatalog(\'' . $this->_baseUrl . '/AjaxChangePagCatalog/change' . '\', ' . $idElemento . ', ' . ($currentPage + 1) . ', ' . $idSitio . ', ' . $tipoVista . ', ' . $idCatalogo . ', ' . $editMode . ', ' . $previewMode . ', ' . (($desdeTienda == false) ? 0 : 1) . ');"> &gt;&gt; </a>';
        }

        return $paginacionHTML;
    }

    static function buildHTMLCatalogoTipoGaleriaAnim($productsAndImgs, $view, $idElemento, $idSitio, $editMode, $tienda, $esDeVenta, $nomFix, $catalogo, $preview, $desdeTienda, $prodBySlide = 3)
    {
        $content_html = '';
        $functionShowProduct = '';
        if (($productsAndImgs == false) || (is_array($productsAndImgs) && count($productsAndImgs) <= 0)) :
            ?>
            <!--            <div class="notice" style="text-align: center; width:inherit;"> No hay informaci&oacute;n disponible de los productos </div>-->
            <div></div>
            <?php
        else :
            //Verificando que la tienda del sitio este configurada para payPal
            //Para las vistas de ejemplos, la variable $tienda llegara con true
            $tiendaConfig = '';
            if ($esDeVenta === true)
            {
                $paymentForm = new Helper_PaymentForm($tienda);
                if (!$paymentForm->checkConfigPaymentForm())
                    ;
                $tiendaConfig = $paymentForm->getMessage();
            }

            $idx = 0;
            $content_html .= '<div class="contentGalCarousel catalogoGaleriaAnim"> ';
            $content_html .= '<ul class="catalogoGaleria catalogCarousel jcarousel-skin-ie7" id="catalogCarousel_' . $idElemento . '">';

            $fixHelper = new Helper_FixHelper();
            $numProducts = count($productsAndImgs);
            foreach ($productsAndImgs as $product)
            {
                if (($editMode == true) || ($product->orderStatus && $preview == true) || ($product->orderStatus && !$editMode && !$preview))
                {
                    //Funcion asociada a los productos del catalogo para verlos en vista de elementos, se les asocia un apuntador de mouse a manera de link
                    //$functionShowProduct = ($editMode == false && $preview == false) ? 'onclick="itemSelectedFromCatalog(' . $idElemento . ', ' . $product->id . ', \'' . $this->getInithPath($idSitio) . '\');"' : '';
                    $functionShowProduct = ($editMode == false && $preview == false) ? 'onclick="itemSelectedFromCatalog(\'' . $fixHelper->generateFix($product->nombre) . '\', ' . $product->id . ', \'' . self::getInithPath($idSitio, $view->getBaseUrlPublic()) . '\');"' : '';
                    $showPointerMouse = ($editMode == false) ? 'onmouseover="this.style.cursor=\'pointer\';"' : '';

                    $verticalSeparator = ( $numProducts > 1) ? 'verticalSeparatorRight' : '';
                    $verticalSeparator = (( $idx == $prodBySlide - 1 || $idx == $prodBySlide) && $numProducts <= $prodBySlide) ? '' : $verticalSeparator;
                    $content_html .= '<li class="wrapper-sf-producto-container-animated ' . $verticalSeparator . '">';
                    $content_html .= '<div  class="catItem"  id="itemGaleria_' . $idElemento . '_' . $product->id . '" ' . $functionShowProduct . ' ' . $showPointerMouse . ' >';
                    $content_html .= self::buildHTMLCatalogoTipoGaleriaItem($product, $view, $idElemento, $tienda);
                    $content_html .= '<input type="hidden" id="p-' . $product->id . '" class="key"/>';
                    $content_html .= '</div>';
                    $content_html .= '</li>';
                    $idx++;
                }
            }
            $content_html .= '</ul>';
            $content_html .= '<input type="hidden" name="totalElements" id="totalElements_' . $idElemento . '" value="' . $idx . '">';
            $content_html .= '</div>';
        endif;
        return $content_html;
    }

    static function buildRelatedProducts($catalog, $initialProduct)
    {
        $html = '';        

//        $tagsRelated = $productosVsTagsAdapter->getTagsByProduct($initialProductId);
//        $tagsRelatedArray = array();
//
//        if (is_array($tagsRelated) && count($tagsRelated) > 0)
//        {
//            foreach ($tagsRelated as $key => $tagObject)
//            {
//                $tagsRelatedArray[] = '"' . $tagObject->valor . '"';
//            }
//        }
//
//        $productsAndImgs = $productosAdapter->getRelatedProducts($catalogId, $initialProductId, $tagsRelatedArray);
//        if (is_array($productsAndImgs) && count($productsAndImgs) > 0)
//        {
//            $html = '<br/><br/>' . self::buildHTMLCatalogoTipoGaleriaAnim($productsAndImgs, $view, -1, $siteId, $editMode, $tienda, $esDeVenta, $view->nomFix, $catalogo, $preview, $desdeTienda) . '<br/><br/>';
//            $html .= '<input type="hidden" name="prodsBySlide" id="prodsBySlide_-1" value="3">';
//        }

        return $html;
    }

    public function buildByProducts($products)
    {
        $imagesAdapter = new Data_ImagenesProductos();
        $productsAndImgs = $imagesAdapter->getProductosWithImages($products);
        $content_html = $this->buildHTMLCatalogGallery($productsAndImgs, null, 0, 1);
        return $content_html;
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
