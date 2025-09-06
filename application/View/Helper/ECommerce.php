<?php

class View_Helper_ECommerce extends Model3_View_Helper {

    const EMPTY_CART = 1;

    public function shoppingCart() {
        //unset ($_SESSION['__M3']['Namespaces']['commerce']);
        $commerce = new Model3_Session_Namespace('commerce');
        $cart = $commerce->cart;
        $htmlCart = '';
        $htmlCart .= '<div id="shopping_details" >';
        $htmlCart .= '<input type="hidden" id="cart_view" value="compact"/>';
        if ($cart) {
            $htmlCart .= '<table class="cartInfo">';
            $htmlCart .= '<tr>';
            $htmlCart .= '<th colspan="4">';
            //Mostramos la cantidad total de articulos en el carrito
            $plural = $cart->getItemCount() == 1 ? '' : 's';
            $htmlCart .= '<img alt="carrito" title="Carrito de compras." src="' . $this->_view->getBaseUrlPublic() . '/images/ui/kart.png"/>Carrito de compras<br/>';
            $htmlCart .= '<span>( ' . $cart->getItemCount() . ' Artículo' . $plural . ')</span>';
            $htmlCart .= '</th>';
            $htmlCart .= '</tr>';
            // Mostramos los productos en el carrito
            $names = $cart->getItemName();
            $variants = $cart->getVariants();
            $prices = $cart->getItemPrices();
            $qtys = $cart->getItemQtys();
            foreach ($cart->getItems() as $item) {
                $htmlCart .= '<tr>';
                $htmlCart .= '<td style="vertical-align:top;">';
                $htmlCart .= '<input class="item-qty" type="text" size="1" id="item-id-' . $item . '" name="item_qty[ ]" value="' . $qtys[$item] . '"/>';
                $htmlCart .= '</td>';
                $htmlCart .= '<td>';
                $htmlCart .= '<span class="item-name">';
                $htmlCart .= $names[$item] . '<input type="hidden" name="item_name[ ]" value="' . $names[$item] . '"/>';
                $htmlCart .= '<input type="hidden" name="item_id[ ]" value="' . $item . '"/>';
                $htmlCart .= '</span>';
                $htmlCart .= '<br/>';
                $htmlCart .= '<span class="item-price">';
                $htmlCart .= '<span style="text-align:right;">$' . number_format($prices[$item], 2) . ' c/u</span>';
                $htmlCart .= '<input type="hidden" name="item_price[ ]" value="' . $prices[$item] . '" />';
                $htmlCart .= '</span>';
                $htmlCart .= '<a class="remove fromcart" id="' . $item . '"><img alt="eliminar" title="Eliminar del carrito." src="' . $this->_view->getBaseUrlPublic() . '/images/ui/close.gif"/></a>';
                $htmlCart .= '<hr/>';
                $htmlCart .= '</td>';
                $htmlCart .= '</tr>';
            }
            if ($cart->getItemCount() > 0) {
                $htmlCart .= '<tr>';
                $htmlCart .= '<th colspan="4">';
                //mostramos el subtotal a pagar
                $htmlCart .= 'Subtotal:<strong> $' . number_format($cart->getSubtotal(), 2) . '</strong>';
                //$htmlCart .= '<br/><br/><a class="cart" id="empty-cart"><img alt="Vaciar" title="Vaciar carrito." src="'.$this->_view->getBaseUrlPublic().'/images/ui/empty_cart.gif"/></a>';
                $htmlCart .= '<br/><br/>';
                $htmlCart .= '<a class="cart" href="' . $this->_view->linkViewCart . '"><img alt="Comprar" title="Comprar." src="' . $this->_view->getBaseUrlPublic() . '/images/ui/buy_cart.gif" style="float:left;margin-left:10px"/>Comprar</a>';
                $htmlCart .= '</th>';
                $htmlCart .= '</tr>';
            }
            $htmlCart .= '</table>';
            $htmlCart .= '<hr/>';
        }
        $htmlCart .= '<div id="cart_notices" style="display:none;"></div>';
        $htmlCart .= '</div>';

        return $htmlCart;
    }

    public function cartDetails($showPayPalButton, $modeDeliveryService, $tienda, $pagoContraEntrega, $action) {
        $this->_view->getJsManager()->addJs('view/helper/ecommerce.js');
        $this->_view->getJsManager()->addJsVar('urlUpdateToCart', '"' . $this->_view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'updateQty')) . '"');
        $this->_view->getJsManager()->addJsVar('urlRemoveFromCart', '"' . $this->_view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'removeFromCart')) . '"');
        $this->_view->getJsManager()->addJsVar('urlEmptyCart', '"' . $this->_view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'emptyCart')) . '"');
        ?>
        <input type="hidden" id="txtBaseUrl" name="txtBaseUrl" value="<?php echo $this->_view->getBaseUrl() ?>" disabled="true">
        <?php
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];

        $commerce = new Model3_Session_Namespace('commerce');
        $cart = $commerce->cart;
        $totalQuantity = 0;
        $disabled = $action == "review" ? 'disabled = "disabled"' : 'required="required"';
        $totalAPagarCreditos = 0;

        if ($cart || $tienda) {
            if ($cart) {
                $totalQuantity = $cart->getItemCount();
                $envio = 0;
                $idEnvio = 0;
                if (is_array($this->_view->metodos_entrega) && count($this->_view->metodos_entrega) > 0) {
                    $idEnvio = $this->_view->metodos_entrega[0]->id;

                    if ($modeDeliveryService == 0) {
                        $envio = $this->_view->metodos_entrega[0]->precio;
                    } else {
                        $qtyElements = 0;
                        $qtys = $cart->getItemQtys();
                        foreach ($cart->getItems() as $item) {
                            $qtyElements += $qtys[$item];
                        }
                    }

                    //definimos el costo de entrega
                    $cart->setShippingCost($envio);
                    $cart->setIdShipping($idEnvio);
                }
            }
            if ($totalQuantity <= 0 && $tienda == NULL) {
                //tomamos en cuenta que si no tiene productos el carrito y esta en review le decimos que se generara una orden
//                echo self::EMPTY_CART;
                if ($this->_view->action == 'review')
                    echo '<div class="ui-widget" id=""><div style="padding:1em;" class="ui-widget-content ui-corner-all">El pedido se realizará sin productos</div></div>';
                else
                    echo '<div class="ui-widget" id=""><div style="padding:1em;" class="ui-widget-content ui-corner-all">Carrito de compras vacio</div></div>';
                echo '</div>';
                return;
            }
            echo '<br/>';
            echo '<div id="shopping_details">';
            echo '<input type="hidden" id="cart_view" value="full"/>';
            echo '<table class="cartInfo table" cellspacing=0>';
            echo '<tr><td colspan="7"><h2>Tienda:   ' . $tienda->getCommercialName() . '</h2></td></tr>';
            echo '<tr><td colspan="7"><h3>Carrito de compras.</h3></td></tr>';
            if ($totalQuantity > 0 && $tienda) {
                echo '<tr>';
                echo '<th></th>';
                echo '<th>';
                echo 'Art&iacute;culo';
                echo '</th>';
                echo '<th>';
                echo 'Precio unitario';
                echo '</th>';
                echo '<th>';
                echo 'Cantidad';
                echo '</th>';
                 echo '<th>';
                echo 'Precio';
                echo '</th>';
                echo '<th>';
                echo 'Cr&eacute;ditos';
                echo '</th>';
                if ($action != "review") {
                    echo '<th>';
                    echo 'Eliminar';
                    echo '</th>';
                }
                echo '</tr>';

                foreach ($cart->getItems() as $id => $item) {
                    $product = $em->find('DefaultDb_Entities_Product', $id);
                    if ($product) {
                        if (isset($item['variant']) && count($item['variant']) > 0) {
                            foreach ($item['variant'] as $idVariant => $variantCart) {
                                $variant = $em->find('DefaultDb_Entities_ProductVariants', $idVariant);
                                $productName = $productName = $product->getName() . ' - ' . $variant->getDescription();
                                echo '<tr class="storeItem">';
                                echo '<td class="imgWrap" style="vertical-align:top;">';
                                //echo $imgs[$item];
                                echo '</td>';
                                // Imagen y datos del producto
                                echo '<td>';
                                echo '<span class="item-name">';
                                echo '<strong style="font-size:medium;">' . $productName . '</strong><input type="hidden" name="item_name[ ]" value="' . $productName . '"/>';
                                echo '<br/><br/>';
                                echo '<span style="font-weight:normal;font-size:smaller;">Precio unitario: <strong>$' . number_format($product->getPrice(), 2) . '</strong></span><input type="hidden" name="item_price[ ] value="' . $product->getPrice() . '"/>';
                                echo '<input type="hidden" name="item_id[ ]" value="' . $id . '_' . $idVariant . '"/>';
                                echo '</span>';
                                echo '</td>';
                                //Cantidad y subtotal producto
                                echo '<td>';
                                echo '<span class="item-price">';
                                echo '<input class="item-qty" type="text" size="1" id="item-id-' . $id . '_' . $idVariant . '" name="item_qty[ ]" value="' . $variantCart['quantity'] . '"/>';
                                echo '<strong style="font-size:11px;">  $' . number_format($product->getPrice() * $variantCart['quantity'], 2) . '</strong>';
                                echo '<span/>';
                                echo '</td>';
                                //Remover producto
                                echo '<td>';
                                echo '<a class="remove" id="' . $id . '_' . $idVariant . '"><img alt="eliminar" title="Eliminar del carrito." src="' . $this->_view->getBaseUrlPublic() . '/images/iconos/tacha.png"/></a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr class="storeItem">';
                            echo '<td class="imgWrap" style="vertical-align:top;">';
                            //echo $imgs[$item];
                            echo '</td>';
                            // Imagen y datos del producto
                            echo '<td>';
                            echo '<span class="item-name">';
                            echo '<strong style="font-size:medium;">' . $product->getName() . '</strong><input type="hidden" name="item_name[ ]" value="' . $product->getName() . '"/>';
                            //echo '<br/><br/>';                    
                            echo '<input type="hidden" name="item_id[ ]" value="' . $id . '"/>';
                            echo '</span>';
                            echo '</td>';
                            echo '<td>';
                            echo '<span style="font-weight:normal;font-size:11px;"><strong>$' . number_format($product->getPrice(), 2) . '</strong></span><input type="hidden" name="item_price[ ] value="' . $product->getPrice() . '"/>';
                            echo '</td>';
                            //Cantidad y subtotal producto
                            echo '<td>';
                            echo '<span class="item-price">';
                            echo '<input class="item-qty form-control" type="text" ' . $disabled . ' size="1" id="item-id-' . $id . '" name="item_qty[ ]" value="' . $item['quantity'] . '"/>'; 
                            echo '<span/>';
                            //echo '<br />';
                            echo '</span>';
                            echo '</span>';
                            echo '</td>';
                            echo '<td>';
                            echo '<strong id="item-precio-'.$id.'" style="font-size:11px;">  $' . number_format($product->getPrice() * $item['quantity'], 2) . '</strong>';
                            echo '</td>';
                            echo '<td>';
                            echo '<input type="hidden" name="costoCreditosProducto-' . $id . '" id="costoCreditosProducto-' . $id . '" value="' . $product->getPriceCreditos() . '" />';
                            echo'<span style="font-weight:normal;font-size:11px;"><strong>&nbsp; <span class="creditosItem" id="item-creditos-' . $id . '">' . number_format($product->getPriceCreditos() * $item['quantity'], 2) . '</span></strong>';
                            $totalAPagarCreditos = $totalAPagarCreditos + ($product->getPriceCreditos() * $item['quantity']);
                            echo '</td>';
                            //Remover producto
                            if ($action != "review") {
                                echo '<td>';
                                echo '<a class="remove" id="' . $id . '"><img alt="eliminar" title="Eliminar del carrito." src="' . $this->_view->getBaseUrlPublic() . '/images/iconos/tacha.png"/></a>';
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                    }
                }
                echo '<!--<tr>';
                echo '<td colspan="7">';
                echo '<div id="cart_notices" style="display:none;"></div>';
                echo '</td>';
                echo '</tr>';
                //Total antes de agregar gastos de envio
                echo '<tr class="rowSubtotal">
                    <td colspan="4">&nbsp;</td>';
                echo '<td>';
                //mostramos el subtotal a pagar
                //echo '<hr/>';
                echo '<span style="float:right;margin:5px 5px 5px 0;">';
                echo '<strong>SubTotal:</strong></span></td>
                                <td  class="subtotalQty"><span style="float:right; margin:8px;"><strong>  $' . number_format($cart->getSubtotal(), 2) . '</strong>';
                //echo '<br><a id="empty-cart">Cancelar compra<a/></br>';
                echo '</span>';
                echo '</td>';
                echo '<td>&nbsp;';
                echo '</td>';
                echo '</tr>';
                //Elegir la forma de pago
                echo '<tr>';
                echo '<td  >';
                //echo '<strong>Elige la forma de pago:</strong>';
                echo '</td >';
                echo '<td colspan="6" >';

                echo '</td>';
                echo '</tr>-->';
                if ((is_null($showPayPalButton)) || ($showPayPalButton == 0)) {
                    
                } else {
                    if ($pagoContraEntrega == false && ( is_array($this->_view->metodos_entrega) && count($this->_view->metodos_entrega) > 0 && ( $tienda->free_delivery_service <= -1 || ($tienda->free_delivery_service >= ($cart->getPaymentAmount() - $cart->getShippingCost())) ))) {
                        //Métodos de envio
                        echo '<tr>
                            <td>&nbsp;</td>';
                        echo '<td>';
                        echo '<table class="envio" style="margin:0px; display:none; ">
                                    <tr>
                                        <th colspan="2">';
                        echo '<strong>Método de envío : </strong>
                                        </th>
                                    </tr>';
                        $count = 0;

                        foreach ($this->_view->metodos_entrega as $entrega) {
                            echo '<tr class="item" style="text-align:left;">';

                            //Obteniendo el valor del envio
                            $deliveryServicePrice = 0;
                            if ($modeDeliveryService == 0) {
                                //Es un precio por compra-pedido
                                $deliveryServicePrice = $entrega->precio;
                            } else {
                                //El valor debe multiplicarse por la cantidad de productos del pedido
                                $qtyElements = 0;
                                $qtys = $cart->getItemQtys();
                                foreach ($cart->getItems() as $item) {
                                    $qtyElements += $qtys[$item];
                                }

                                $deliveryServicePrice = $entrega->precio * $qtyElements;
                                $deliveryServicePrice = ($tienda->max_cost_delivery_service < 0) ? $deliveryServicePrice : ( ($deliveryServicePrice >= $tienda->max_cost_delivery_service) ? $tienda->max_cost_delivery_service : $deliveryServicePrice );
                            }


                            $selected = '';
                            if ($cart->getIdShipping() <= 0) {
                                if ($count === 0)
                                    $selected = 'checked="checked"';
                            }
                            else {
                                if ($cart->getIdShipping() == $entrega->id)
                                    $selected = 'checked="checked"';
                            }

                            if ($selected != '') {
                                $cart->setIdShipping($entrega->id);
                                $cart->setShippingCost($deliveryServicePrice);
                            }

                            if ($deliveryServicePrice <= 0) {
                                echo '<td colspan = "2" ><input id="' . $entrega->id . '" class="shipping_cost" type="radio" name="shipping" ' . $selected . ' value="' . $deliveryServicePrice . '"/>';
                                echo $entrega->nombre . '</td>';
                            } else {
                                echo '<td><input id="' . $entrega->id . '" class="shipping_cost" type="radio" name="shipping" ' . $selected . ' value="' . $deliveryServicePrice . '"/>';
                                echo $entrega->nombre . '</td><td> $' . number_format($deliveryServicePrice, 2) . ' ' . $currency . '</td>';
                            }
                            echo '</tr>';
                            $count++;
                        }
                        echo '</table>';
                        echo '</td>';
                        echo '</tr>';
                    } else {//si la tienda tiene activa la opcion de gratis del envio o si es pago contra entrega
                        if (($tienda->free_delivery_service >= 0 && ($tienda->free_delivery_service <= ($cart->getPaymentAmount() - $cart->getShippingCost()))) || $pagoContraEntrega == true) {
                            $envioGratis = '';
                            $envioGratis .= '<tr class="envioFree">';
                            $envioGratis .= '  <td>&nbsp;</td>';
                            $envioGratis .= '  <td>';
                            $envioGratis .= '      <span style="float:right;margin:5px 5px 5px 0;">';
                            $envioGratis .= '          <strong>Env&iacute;o :</strong>';
                            $envioGratis .= '      </span>';
                            $envioGratis .= '  </td>';
                            $envioGratis .= '  <td class="subtotalQty"><span><strong> $ 0.00 </strong></span></td>';
                            $envioGratis .= '  <td>&nbsp;</td>';
                            $envioGratis .= '</tr>';

                            echo $envioGratis;
                        }
                    }
                }

                //Total a pagar
                echo '<tr class="rowTotal">';
                echo '<td colspan="4" >&nbsp;</td>';
                echo '<td>&nbsp;</td>
                      <td>';
                // echo '<hr/>';
                echo '<span style="float:right; margin:8px;">';
                $totalAPagar = $cart->getSubtotal();
                echo '<style>';
                echo '#empty-cart:hover, .remove:hover{cursor:pointer;}';
                echo '</style>';
                echo '<strong> Total a pagar :</strong></span></td>
                             <td class="cartTotal"><strong><span id="total_payment" style="float:right; margin:8px;">$' . number_format($totalAPagar, 2) . '</span></strong></td>';
                echo '</td>';
                echo '</tr>';                
                
                //Total a pagar en creditos
                echo '<tr class="rowTotal">';
                echo '<td colspan="4" >&nbsp;</td>';
                echo '<td>&nbsp;</td>
                        <td>';
                // echo '<hr/>';
                echo '<span style="float:right; margin:8px;">';
                echo '<style>';
                echo '#empty-cart:hover, .remove:hover{cursor:pointer;}';
                echo '</style>';
                echo '<strong> Conversi&oacute;n en cr&eacute;ditos:</strong></span></td>
                      <td class="cartTotal"><span id="total_paymentCreditos" style="float:right; margin:8px;color:green;">' . number_format($totalAPagarCreditos, 2) . '</span></td>';
                echo '</td>';                
                echo '</tr>';
                
                echo '<tr>';
                echo '<td colspan="7" class="cartTotal"  style="text-align:center;"><a class="" style="float:right;" id="empty-cart"><img alt="Cancelar" title="Cancelar compra." src="' . $this->_view->getBaseUrlPublic() . '/images/ui/cancelar-carrito.png"/></a></td>';
                echo '</tr>';
                echo '</table>';
                echo '</div>';
            }
            if ($this->_view->branchesBuyer) {   // se cambio la accion de review a confirmOrder
                echo '<form method="post" action="' . $this->_view->url(array('module' => 'User', 'controller' => 'Store', 'action' => 'confirmOrder')) . '">';
                echo '<input type="hidden" name="routePointId" id="routePointId" value="' . $this->_view->routePointId . '"> ';
                //Se genera un pedido Manual 
                echo '<div id="shopping_details1"> 
                    <table class="table">
                    <tr>
                    <td style="text-align:left; vertical-align:text-top;"><strong>Comentarios de mi pedido y entrega, si tienes varias ubicaciones registradas por favor especifica aqu&iacute; cual ser&aacute; el destino:</strong></td>
                    </tr>
                    <tr>
                    <td colspan="3"><center><textarea id="comments" name="comments" style="width: 90%; height: 100px;">' . ($this->_view->comments ? $this->_view->comments : '') . '</textarea></center></td>
                    </tr>
                    <tr>
                    <td>
                    </td>
                    </tr>
                    </table>
                    </div>';
                $this->printPagoOrden($tienda->getCrdPay(),$tienda->getDelyPay());

                if ($tienda) {
                    echo '<input name="store" type="hidden" value="' . $tienda->getId() . '"/>';
                }
                
                if ($action != "review") {
                    echo '<div id="shopping_details3"> ';
                    echo '<input class=" btn-default sin-padding btn-generar-pedido" type="submit" />';
                    echo '</div>';
                }
                echo '</form>';
                echo '<br /><br />';
            } else {                
                echo '<div class="alert">No se puede generar pedido ya que no cuentas con sucursales o puntos de entrega</div>';
            }
            
            
        } else {
            if ($this->_view->action == 'review')
                echo '<div class="ui-widget" id=""><div style="padding:1em;" class="ui-widget-content ui-corner-all">El pedido se realizará sin productos</div></div>';
            else
                echo '<div class="ui-widget" id=""><div style="padding:1em;" class="ui-widget-content ui-corner-all">Carrito de compras vacio</div></div>';
            echo '</div>';
        }
    }

    public function printPagoOrden($crd,$dely) {
        echo "<div class='container' id='shopping_details2'>";
        echo "  <div class='row' >";
        echo "      <div class='blockInner'>";
        $this->printDebitar($crd,$dely);
        echo "      </div>";
        echo "  </div>";
        echo "</div>";
    }

    public function selectPointsOrigenDestiny() {
        $inputHidden = $disabled = $selected = "";
        $branchPoint = $branchBuyer = false;
        $pointsellerops = "";
        $pointbuyerops = "";
        if ($this->_view->branchesSeller):
        ?>
                        <span style="">Seleccione Fecha:</span>
                        <select id="boxSchedules" name="boxSchedules" style="width: 45%;">
                            <option value="0">Seleccionar...</option>
                        </select><br /><br />
       <?php endif;
                
        
        if ($this->_view->branchesSeller):
            foreach ($this->_view->branchesSeller as $pOrigen):
                if ($pOrigen->getPoint())
                    $branchPoint = TRUE;
            endforeach;
            if ($branchPoint) {
                ?>
                Elige la sucursal de Origen:
                <select name="pointSeller" id="pointSeller">
                <?php foreach ($this->_view->branchesSeller as $pOrigen): 
                		$selected = '';
                		$verfiusr = '';
                        if ($this->_view->order)
                            if ($this->_view->order->getPointSeller()) {
                                if ($pOrigen->getPoint()->getId() == $this->_view->order->getPointSeller()->getId()) {
                                    $selected = "selected ='SELECTED'";
                                    $verfiusr = $this->_view->order->getSeller()->getId();
                                }
                            }
 						if($selected !== '' && $verfiusr ===24){                        
							$pointsellerops = '<option value="'.$pOrigen->getId().'" '. $selected .'>'. $pOrigen->getName().'</option>';
							break;
                        }else{
							$pointsellerops .= '<option value="'.$pOrigen->getId().'" '. $selected .'>'. $pOrigen->getName().'</option>';
						}
            	?>
               <!-- option value="<?php /*echo $pOrigen->getId() ?>" <?php echo $selected ?> > <?php echo $pOrigen->getName();*/ ?></option-->
                <?php endforeach; 
                	echo $pointsellerops; 
                 ?>
                </select>
            <?php
            }
        endif;
        if ($branchPoint) {
            if ($this->_view->branchesBuyer):
                foreach ($this->_view->branchesBuyer as $pDestiny):
                    if ($pDestiny->getPoint())
                        $branchBuyer = true;
                endforeach;
                if ($branchBuyer) {
                    if ($this->_view->order) {
                        $disabled = $this->_view->order->getPointBuyer() ? "disabled = 'disabled'" : '';
                    }
                    ?>
                    &nbsp; &nbsp; Elige la sucursal de Destino
                    <select name="pointBuyer" id="pointBuyer" <?php /*echo $disabled*/ ?>>
                    <?php
                    foreach ($this->_view->branchesBuyer as $pDestiny):
                        $selected = '';
                    	$verfiusr = '';
                    
                        if ($this->_view->order)
                            if ($this->_view->order->getPointBuyer()) {
                                if ($pDestiny->getPoint()->getId() == $this->_view->order->getPointBuyer()->getId()) {
                                    $selected = "selected ='SELECTED'";
                                    $verfiusr = $this->_view->order->getBuyer()->getId();
                                    $inputHidden = '<input type="hidden" name="pointBuyer" value="' . $pDestiny->getId() . '" />';
                                }
                            }
                        if($selected !== '' && $verfiusr ===24){
                        	$pointbuyerops = '<option value="'.$pDestiny->getId().'" '.$selected.'>'.$pDestiny->getName().'</option>';
                        	break;
                        }else{
							$pointbuyerops .= '<option value="'.$pDestiny->getId().'" '.$selected.'>'.$pDestiny->getName().'</option>';
                        }
                        ?>
                    	<!-- option value="<?php /*echo $pDestiny->getId() ?>"<?php echo $selected ?>> <?php echo $pDestiny->getName();*/ ?></option-->
                    <?php endforeach; 
                    	echo $pointbuyerops;
                    ?>
                    
                    </select>
                        <?php echo $inputHidden; ?>
                    <br/>
                        <?php
                    }
                endif;?>
            <!--        <div id="shippingDateInformation"></div>
                    <input type="hidden" name="shippingDate" id="shippingDate" value="">
                    <div class="clear" style="margin-top: 15px;"></div>-->
            <?php
        }
        else {
            ?>
            <div class="alert">
                No se puede estimar el tiempo de entrega por que la sucursal no tiene un punto de entrega o recoleccion definido
            </div>
            <?php
        }
    }

    public function cartDetailsOfRoom($showPayPalButton, $modeDeliveryService, $tienda, $pagoContraEntrega) {
        $commerce = new Model3_Session_Namespace('commerce');
        $roomAdapter = new Model_Reservaciones_Habitaciones();
        $room = $roomAdapter->findOne('id_sitio', $tienda->id_sitio);
        $cero = 0;
        $cart = $commerce->cart;

        switch ($tienda->moneda) {
            case M3Commerce_Class_SalesProcess::MXN :
                $currency = M3Commerce_Class_SalesProcess::MXN_STR;
                break;
            case M3Commerce_Class_SalesProcess::USD :
                $currency = M3Commerce_Class_SalesProcess::USD_STR;
                break;
            default:
                $currency = M3Commerce_Class_SalesProcess::MXN_STR;
                break;
        }

        if ($cart) {
            $totalQuantity = $cart->getItemCount();
            if ($totalQuantity <= 0) {
                echo self::EMPTY_CART;
//               echo '<div class="ui-widget" id=""><div style="padding:1em;" class="ui-widget-content ui-corner-all">Carrito de compras vacio</div></div>';
//               echo '</div>';
                return;
            }
            echo '<br/>';
            echo '<div id="shopping_details">';
            echo '<input type="hidden" id="cart_view" value="full"/>';
            echo '<table class="cartInfo" cellspacing=0>';
            echo '<tr><td><h3>Habitaciones para reservar.</h3></tr></td>';
            echo '<tr>';
            echo '<th></th>';
            echo '<th>';
            echo 'Habitaci&oacute;n';
            echo '</th>';
            echo '<th>';
            echo 'Cantidad/Precio';
            echo '</th>';
            echo '<th>';
            echo 'Opciones';
            echo '</th>';
            echo '</tr>';
            // Mostramos los productos en el carrito
            $names = $cart->getItemName();
            $prices = $cart->getItemPrices();
            $qtys = $cart->getItemQtys();
            $imgs = $cart->getItemImg();
            $adults = $cart->getItemAdults();
            $childs = $cart->getItemChild();
            $costoAdd = $cart->getItemPriceAdd();
            $additionalAdults = $cart->getItemAdd();
            $costoAdditionalChilds = $cart->getItemPriceAdditionalChilds();
            $additionalsChilds = $cart->getItemAdditionalsChilds();



            foreach ($cart->getItems() as $item) {
                echo '<tr class="storeItem">';
                echo '<td class="imgWrap" style="vertical-align:top;">';
                echo $imgs[$item];
                echo '</td>';
                // Imagen y datos del producto
                echo '<td>';
                echo '<span class="item-name">';
                echo '<strong style="font-size:medium;">' . $names[$item] . '</strong><input type="hidden" name="item_name[ ]" value="' . $names[$item] . '"/>';
                echo '<br/><br/>';
                echo '<span style="font-weight:normal;font-size:smaller;">Precio unitario: <strong>$' . number_format($prices[$item], 2) . ' ' . $currency . '</strong></span><input type="hidden" name="item_price[ ] value="' . $prices[$item] . '"/>';
                echo '<input type="hidden" name="item_id[ ]" value="' . $item . '"/>';
                echo '</span>';
                echo '</td>';
                //Cantidad y subtotal producto
                echo '<td>';
                echo '<span class="item-price">';
                echo '<strong style="font-size:11px;">' . $adults[$item] . '&nbsp; &nbsp; Adultos  </strong><br/><br/>';
                echo '<strong style="font-size:11px;">' . $childs[$item] . '&nbsp; &nbsp; Menores  </strong><br/><br/>';
                if ($additionalAdults[$item] > 0) {
                    echo '<strong style="font-size:11px;"> ' . $additionalAdults[$item] . ' &nbsp; &nbsp; Adultos Adicionales </strong><br/><br/>';
                }
                if ($additionalsChilds[$item] > 0) {
                    echo '<strong style="font-size:11px;"> ' . $additionalsChilds[$item] . ' &nbsp; &nbsp; Menores Adicionales </strong><br/><br/>';
                }
                /**
                  echo '<input class="item-qty-room" type="text" size="1" id="item-id-'.$item.'" name="item_qty[ ]" value="'.$qtys[$item].'"/>';
                  echo '<strong style="font-size:11px;">  $'.number_format($prices[$item] * $qtys[$item],2).' '.$currency.'</strong>';
                 */
                echo '<span/>';
                echo '</td>';
                //Remover producto
                echo '<td>';
                echo '<a class="removeRoom" id="' . $item . '"><img alt="eliminar" title="Eliminar de Reservados" src="' . $this->_view->getBaseUrlPublic() . '/images/iconos/tacha.png"/></a><br/><br/>';
                echo '<a class="editar" id="' . $item . '"><img alt="editar" title="Edita la habitaci&oacute;n" src="' . $this->_view->getBaseUrlPublic() . '/images/ui/edit16.png"/></a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="4">';
            echo '<div id="cart_notices" style="display:none;"></div>';
            echo '</td>';
            echo '</tr>';
            //Total antes de agregar gastos de envio
            echo '<tr class="rowSubtotal">
                    <td>&nbsp;</td>';
            echo '<td>';
            //mostramos el subtotal a pagar
            //echo '<hr/>';
            echo '<span style="float:right;margin:5px 5px 5px 0;">';
            echo '<strong>SubTotal:</strong></span></td> 
                                <td  class="subtotalQty"><span><strong>  $' . number_format($cart->getSubtotal(), 2) . ' ' . $currency . '</strong>';
            //echo '<br><a id="empty-cart">Cancelar compra<a/></br>';
            echo '</span>';
            echo '</td>
                       <td>&nbsp;</td>';
            echo '</tr>';
            //Elegir la forma de pago
            echo '<tr>';
            echo '<td >';
            //echo '<strong>Elige la forma de pago:</strong>';
            echo '</td >';
            echo '<td colspan="3" >'
            ?>
            <?php if ($this->_view->configTienda->use_bank): ?>
                <div class="contentRadioImage">
                    <input  type="radio" align="middle" class="paymentForm paymentDeposit" name="paymentForm" value="deposito" />
                    <img class="radioImage" name="radioImage_deposito" src="<?php echo $this->_view->getBaseUrlPublic(); ?>/images/bank.png"   alt="Transferencia o depósito bancario" />
                </div>
                <?php
            endif;
            if ($this->_view->configTienda->use_paypal):
                ?>
                <div class="contentRadioImage">
                    <input type="radio" align="middle" class="paymentForm paymentPaypal" name="paymentForm" value="paypal" />
                    <img class="radioImage" name="radioImage_paypal" src="https://www.paypal.com/es_XC/i/btn/btn_xpressCheckout.gif"   alt="Check out with PayPal"/>
                </div>
                <?php
            endif;
            if ($this->_view->configTienda->use_personal):
                ?>
                <div class="contentRadioImage">
                    <input type="radio" align="middle" class="paymentForm paymentContra" name="paymentForm" value="contraEntrega"  />
                    <img class="radioImage" name="radioImage_contraEntrega" src="<?php echo $this->_view->getBaseUrlPublic(); ?>/images/personal.png"  alt="Pago contra entrega"/>
                </div>
                <?php
            endif;
            if ($this->_view->configTienda->use_dineromail):
                ?>
                <div class="contentRadioImage">
                    <input type="radio" align="middle" class="paymentForm paymentDineromail" name="paymentForm" value="dineromail"  />
                    <img class="radioImage" name="radioImage_dineromail" src="<?php echo $this->_view->getBaseUrlPublic(); ?>/images/dineromail.png"  alt="DineroMail"/>
                </div>
                <?php
            endif;

            echo '</td>';
            echo '</tr>';

            //Total a pagar
            echo '<tr class="rowTotal">';
            echo '<td>&nbsp;</td>
                        <td>';
            // echo '<hr/>';
            echo '<span style="float:right; margin:8px;">';
            $totalAPagar = $cart->getPaymentAmount();
            echo '<strong> Total a pagar : </strong></span></td>
                             <td class="cartTotal"><strong><span id="total_payment" style="float:right; margin:8px;">$' . number_format($totalAPagar, 2) . ' ' . $currency . '</span></strong></td>';
            echo '<td class="cartTotal"  style="text-align:center;"><a class="" style="float:right;" id="empty-cart-room"><img alt="Cancelar" title="Cancelar la Reservaci&oacute;n." src="' . $this->_view->getBaseUrlPublic() . '/images/ui/empty_cart.gif"/>Cancelar<a/></td>';
            echo '</td>';
            echo '</tr>';
            $this->printPaymentButtons($showPayPalButton);
            echo '</table>';
            // echo '';
            echo '</div>';
        }
    }

    protected function printPaymentButtons($showPayPalButton) {

        echo '<tr class="rowPayPal"><td>&nbsp;</td>
        <td id="rowPayment" colspan="3" style="padding:5px;">';
//        if($this->_view->configTienda->use_bank)
//        {
//            echo '<form style="float:right;" action="'.$this->_view->url(array('controller'=>'Display','action'=>'review','0'=>$this->_view->nomFix)).'" METHOD="POST">
//                              <input type="image" name="submit" src="'.$this->_view->getBaseUrlPublic().'/images/bank.png" border="0" align="middle" alt="Transferencia o depósito bancario"/>
        //echo '<input type="hidden" name="method" value="'.TRANSFERENCIA.'"/> ';
//                              </form>';
//        }
//
//        if($this->_view->configTienda->use_personal)
//        {
//            echo '<form style="float:right;" action="'.$this->_view->url(array('controller'=>'Display','action'=>'review','0'=>$this->_view->nomFix)).'" METHOD="POST">
//                <input type="image" name="submit" src="'.$this->_view->getBaseUrlPublic().'/images/personal.png" border="0" align="middle" alt="Pago contra entrega"/>
//                <input type="hidden" name="method" value="'.PERSONAL.'"/>
//              </form>';
//        }
//        if($this->_view->configTienda->use_paypal)
//        {
//            if( (is_null($showPayPalButton)) || ($showPayPalButton == 0))
//            {
//
//            }
//            else
//            {
//                echo '<form style="float:right;" action="'.$this->_view->url(array('controller'=>'ExpressCheckout','action'=>'index')).'" METHOD="POST">
//                              <input type="image" name="submit" src="https://www.paypal.com/es_XC/i/btn/btn_xpressCheckout.gif" border="0" align="middle" alt="Check out with PayPal"/>
//                      </form>';
//            }
//        }
        echo '</td></tr>';
    }

    public function printEndButton($metodo) {
        echo '<tr class="rowPayPal"><td colspan="3" style="text-align:right;padding:5px;">';
        switch ($metodo) {
            case PAYPAL :
                echo '<form action="' . $this->_view->url(array('controller' => 'Display', 'action' => 'confirmorder', '0' => $this->_view->nomFix)) . '" METHOD="POST">
                    <input type="image" name="submit" src="https://www.paypal.com/es_XC/i/btn/btn_xpressCheckout.gif" border="0" align="top" alt="Check out with PayPal"/></form>';
                break;
            case TRANSFERENCIA: case PERSONAL:
                echo '<form action="' . $this->_view->url(array('controller' => 'Display', 'action' => 'confirmorder', '0' => $this->_view->nomFix)) . '" METHOD="POST">
                    <input type="image" name="submit" class="endButton-sf" src="' . $this->_view->getBaseUrlPublic() . '/images/order.png" border="0" align="top" alt="Banco"/></form>';
                break;
            case DINEROMAIL :
                //echo '<input type="image" name="dm_submit" src="'.$this->_view->getBaseUrlPublic().'/images/PagaDineroMail.png" border="0" align="top" alt="Pagar con dineromail"/>';
                echo '<img id="dm-submit-btn" style="cursor:pointer;" src="' . $this->_view->getBaseUrlPublic() . '/images/PagaDineroMail.png" border="0" align="top" alt="Pagar con dineromail"/>';
                break;
        }
        echo '</td>';
    }

    public function reviewHeader($metodo) {
        switch ($metodo) {
            case TRANSFERENCIA:
                echo '<h2 class="sf-methodText sf-templateColor sf-backgroundMethod">Transferencia o depósito bancario</h2>';
                //echo '<div class="detailsShoppingDone-sf"><p id="bank_details" >'.$this->_view->configTienda->bank_details.'</p></div>';
                echo '<div class="sf-backgroundWhite"><p id="bank_details" class="sf-detailsOrder">' . $this->_view->configTienda->bank_details . '</p></div>';
                break;
            case PERSONAL:
                echo '<h2 class="sf-methodText sf-templateColor sf-backgroundMethod">Pago contra entrega</h2>';
                //echo '<div class="detailsShoppingDone-sf"><p id="personal_details">'.$this->_view->configTienda->personal_details.'</p></div>';
                echo '<div class="sf-backgroundWhite"><p id="personal_details" class="sf-detailsOrder">' . $this->_view->configTienda->personal_details . '</p></div>';
                break;
        }
        echo '<div class="sf-backgroundMethod">&nbsp;</div>';
    }

    public function shoppingCartSimple() {
        $commerce = new Model3_Session_Namespace('commerce');
        $tiendasAdapter = new Data_Tiendas();
        $tienda = $tiendasAdapter->getStoreForASite($this->_view->sitio['id']);
        $classColorCart = 'cart-grey';
        $htmlCart = '';
        $cat = $this->_view->sitio['categoria'];

        if ($cat != SITIO_WEB && $cat != PREMIUM && $cat != AFILIADO && $this->_view->showShoppingCart == true) {
            if (is_array($tienda) && count($tienda) > 0) {
                $classColorCart = ($tienda[0]->tipo_carrito == '' ) ? $classColorCart : $tienda[0]->tipo_carrito;
            }

            $cart = $commerce->cart;

            $classPer = ($this->_view->sitio['tipo_template'] == 2) ? 'cartLeft' : '';

            $htmlCart .= '<div id="shopping_details" class="wrapper-cartInfoSimple ' . $classPer . '">';
            $htmlCart .= '<input type="hidden" id="cart_view" value="compact"/>';
            $link = '#';
            $count = 0;

            if ($cart) {
                $link = ($this->_view->edition == 1 || $this->_view->preview == 1 ) ? '#' : $this->_view->linkViewCart;
                $count = $cart->getItemCount();
            }

            $htmlCart .= '<table class="cartInfoSimple">';
            $htmlCart .= '<tr>';
            $htmlCart .= '<td>';
            $htmlCart .= '<a title="Ver carrito de compras" class="cart ' . $classColorCart . '" href="' . $link . '">';
            $htmlCart .= '<span>(' . $count . ')</span>';
            $htmlCart .= '</a>';
            $htmlCart .= '</td>';
            $htmlCart .= '</tr>';
            $htmlCart .= '</table>';
            $htmlCart .= '</div>';
            $htmlCart .= '<div style="clear:both;"></div>';
        }

        return $htmlCart;
    }

    private function printDebitar($crd,$dely) {
        ?>
            <table class="table table-condensed">
            <tr>
                <td>
                    <h4>Método de Pago</h4>
                </td>
                <td>
                    <select id="slcTipoPago" name ="slcTipoPago" onchange="fncActualizaTipoPago()" class="form-control" required="true" >
                        <option value="3">Pago Fuera de Mas Distribucion</option>
                        <?php if($crd == "1"){ ?>
                        <option value="2">Pagar ahora con créditos</option>
                        <?php } /*if($dely == "1"){?> //OPCION SUSPENDIDA HASTA REVISAR FUNCIONAMIENTO DE CREDITOS
                        <option value="1">Pago Contra Entrega</option>
                        <?php }*/ ?>
                    </select>
                </td>
                <td>
                    &nbsp;
                </td>
                
                <td>Tipo de Moneda:</td>
                <td> 
                	<div id="monedatxt"></div>
                    <input type="hidden" name="moneda" id="moneda">
                    <input type="hidden" name="slcTipoMoneda" id="slcTipoMoneda"/>
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
            
            <tr>
                <td>Saldo Disponible:</td>
                <td>
	                <div id="txtSaldover"></div>
	                <input type="hidden" id="txtSaldo" name="txtSaldo" />
                </td>
                <td>
                    &nbsp;
                </td>
                <td>Conversi&oacute;n a Cr&eacute;ditos:</td>
                <td> 
                	<span id="txtCreditosver" style="color: green;" ></span>
                    <input type="hidden" id="txtCreditos" name="txtCreditos"/>
                </td> 
            
            </tr>
            
            <tr> <input type="hidden" id="txtIdTipoPago" name="txtIdTipoPago" value="3" ></input>
        </tr>
        </table>

        <?php
        $this->_view->getJsManager()->addJs('view/scripts/User/BackStore/viewEfectuarPago' . VERSION_JS . '.js');
    }
}
